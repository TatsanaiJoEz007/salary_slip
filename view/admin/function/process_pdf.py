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

    def extract_emp_id(text):
        # ใช้ regex เพื่อค้นหาเลขพนักงานที่ขึ้นต้นด้วย "P" และตามด้วยตัวเลข
        match = re.search(r'\bP\d{4,}\b', text)  # เลขพนักงานที่มี 4 ตัวอักษรขึ้นไป
        if match:
            return match.group(0)
        
        # ถ้าไม่พบในรูปแบบข้างต้น ลองหาโดยดูบริบทเพิ่มเติม (เช่นคำว่า "รหัสพนักงาน" หรือ "Emp")
        match = re.search(r'(รหัสพนักงาน|Emp|Employee ID)\s*[:\-]?\s*(P\d+)', text, re.IGNORECASE)
        if match:
            return match.group(2)

        # หากยังไม่พบ ให้ลองค้นหาด้วยรูปแบบอื่น ๆ ตามที่เห็นในไฟล์
        lines = text.split("\n")
        for line in lines:
            if re.match(r'P\d{4,}', line.strip()):
                return line.strip()

        # หากไม่พบเลขพนักงาน ให้คืนค่าเป็น "unknown"
        return 'unknown'

    # แยก emp_id สำหรับครึ่งบนและครึ่งล่าง
    emp_id_top = extract_emp_id(top_half)
    emp_id_bottom = extract_emp_id(bottom_half)

    # บันทึกข้อมูลในครึ่งบนพร้อม path ของไฟล์ PDF
    sql_top = "INSERT INTO tb_payslips (emp_id, page_number, part, content, slip_divide) VALUES (%s, %s, %s, %s, %s)"
    cursor.execute(sql_top, (emp_id_top, page_num + 1, 'top', top_half, top_pdf_path))
    conn.commit()

    # บันทึกข้อมูลในครึ่งล่างพร้อม path ของไฟล์ PDF
    sql_bottom = "INSERT INTO tb_payslips (emp_id, page_number, part, content, slip_divide) VALUES (%s, %s, %s, %s, %s)"
    cursor.execute(sql_bottom, (emp_id_bottom, page_num + 1, 'bottom', bottom_half, bottom_pdf_path))
    conn.commit()

# ปิดการเชื่อมต่อและปิดไฟล์ PDF
cursor.close()
conn.close()
pdf_document.close()

print("PDF data processed successfully.")
