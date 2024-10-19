import fitz  # PyMuPDF
import mysql.connector
import re
import sys
import os

# รับ path ของ PDF จาก command line arguments
pdf_path = sys.argv[1]

# เชื่อมต่อกับฐานข้อมูล MySQL
conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='salary_slip'
)
cursor = conn.cursor()

# เปิดไฟล์ PDF
pdf_document = fitz.open(pdf_path)
permanent_folder = "permanent_pdfs"
os.makedirs(permanent_folder, exist_ok=True)

# อ่านแต่ละหน้าใน PDF
for page_num in range(pdf_document.page_count):
    page = pdf_document.load_page(page_num)
    page_height = page.rect.height

    # แบ่งข้อมูลครึ่งบนและครึ่งล่างของหน้า
    top_half = page.get_text("text", clip=fitz.Rect(0, 0, page.rect.width, page_height / 2))
    bottom_half = page.get_text("text", clip=fitz.Rect(0, page_height / 2, page.rect.width, page_height))

    # สร้างไฟล์ PDF สำหรับครึ่งบนและครึ่งล่าง
    top_half_pdf = fitz.open()
    bottom_half_pdf = fitz.open()

    # ครึ่งบน
    top_rect = fitz.Rect(0, 0, page.rect.width, page_height / 2)
    top_page = pdf_document.load_page(page_num)
    top_page.set_cropbox(top_rect)
    top_half_pdf.insert_pdf(pdf_document, from_page=page_num, to_page=page_num)

    # ครึ่งล่าง
    bottom_rect = fitz.Rect(0, page_height / 2, page.rect.width, page_height)
    bottom_page = pdf_document.load_page(page_num)
    bottom_page.set_cropbox(bottom_rect)
    bottom_half_pdf.insert_pdf(pdf_document, from_page=page_num, to_page=page_num)

    # กำหนด path สำหรับบันทึกไฟล์ถาวร
    top_pdf_path = os.path.join(permanent_folder, f"top_{page_num + 1}.pdf")
    bottom_pdf_path = os.path.join(permanent_folder, f"bottom_{page_num + 1}.pdf")

    # บันทึกไฟล์ PDF ถาวร
    top_half_pdf.save(top_pdf_path)
    bottom_half_pdf.save(bottom_pdf_path)

    def extract_emp_info(text):
        # ใช้ regex เพื่อค้นหาเลขพนักงานที่ขึ้นต้นด้วย "P" และตามด้วยตัวเลข
        emp_id_match = re.search(r'\bP\d{4,}\b', text)  # เลขพนักงานที่มี 4 ตัวอักษรขึ้นไป
        emp_id = emp_id_match.group(0) if emp_id_match else 'unknown'
        
        # ใช้ regex เพื่อค้นหาชื่อพนักงานหลังเลขพนักงาน
        emp_name_match = re.search(r'P\d{4,}\s*(?:ชื่อสกุล|Name|Emp)?[:\-]?\s*([^\n]*)', text, re.IGNORECASE)
        emp_name = emp_name_match.group(1).strip() if emp_name_match else 'unknown'

        # ใช้ regex เพื่อค้นหาแผนกพนักงานหลังคำว่า "Dep."
        emp_dep_match = re.search(r'Dep\.\s*(\S.*)', text)
        emp_dep = emp_dep_match.group(1).strip() if emp_dep_match else 'unknown'

        # แก้ไขคำที่มีสระอำผิดปกติเป็นสระอา
        emp_name = re.sub(r'ำ', 'า', emp_name)
        emp_dep = re.sub(r'ำ', 'า', emp_dep)

        return emp_id, emp_name, emp_dep

    def extract_salary_details(text):
        # ใช้ regex เพื่อดึงข้อมูลรวมเงินได้ (สีเขียว)
        salary_income_match = re.search(r'รวมเงินได้\s*[:\-]?\s*([\d,]+\.\d{2})', text)
        salary_income = float(salary_income_match.group(1).replace(',', '')) if salary_income_match else 0.0

        # ใช้ regex เพื่อดึงข้อมูลรวมเงินหัก (สีแดง)
        salary_deduction_match = re.search(r'รวมเงินหัก\s*[:\-]?\s*([\d,]+\.\d{2})', text)
        salary_deduction = float(salary_deduction_match.group(1).replace(',', '')) if salary_deduction_match else 0.0

        # ใช้ regex เพื่อดึงข้อมูลรวมเงินสุทธิ (สีม่วง)
        salary_net_match = re.search(r'เงินได้สุทธิ\s*[:\-]?\s*([\d,]+\.\d{2})', text)
        salary_net = float(salary_net_match.group(1).replace(',', '')) if salary_net_match else 0.0

        return salary_income, salary_deduction, salary_net

    # แยก emp_id, emp_name, emp_dep และรายละเอียดเงินเดือน สำหรับครึ่งบนและครึ่งล่าง
    emp_id_top, emp_name_top, emp_dep_top = extract_emp_info(top_half)
    salary_income_top, salary_deduction_top, salary_net_top = extract_salary_details(top_half)

    emp_id_bottom, emp_name_bottom, emp_dep_bottom = extract_emp_info(bottom_half)
    salary_income_bottom, salary_deduction_bottom, salary_net_bottom = extract_salary_details(bottom_half)

    # บันทึกข้อมูลในครึ่งบนพร้อม path ของไฟล์ PDF
    sql_top = """
        INSERT INTO tb_payslips (
            emp_id, emp_name, emp_dep, salary_income, salary_deduction, salary_net, page_number, part, content, slip_divide
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(sql_top, (emp_id_top, emp_name_top, emp_dep_top, salary_income_top, salary_deduction_top, salary_net_top, page_num + 1, 'top', top_half, top_pdf_path))
    conn.commit()

    # บันทึกข้อมูลในครึ่งล่างพร้อม path ของไฟล์ PDF
    sql_bottom = """
        INSERT INTO tb_payslips (
            emp_id, emp_name, emp_dep, salary_income, salary_deduction, salary_net, page_number, part, content, slip_divide
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """
    cursor.execute(sql_bottom, (emp_id_bottom, emp_name_bottom, emp_dep_bottom, salary_income_bottom, salary_deduction_bottom, salary_net_bottom, page_num + 1, 'bottom', bottom_half, bottom_pdf_path))
    conn.commit()

# ปิดการเชื่อมต่อและปิดไฟล์ PDF
cursor.close()
conn.close()
pdf_document.close()

print("PDF data processed successfully.")
