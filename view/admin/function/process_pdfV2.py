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

def extract_value(text, label):
    match = re.search(rf"{label}\s*[:\-]?\s*([\d.,]+)", text)
    return float(match.group(1).replace(',', '')) if match else None

def extract_text_value(text, label):
    match = re.search(rf"{label}\s*[:\-]?\s*(.+)", text)
    return match.group(1).strip() if match else None

def extract_date(text, label):
    match = re.search(rf"{label}\s*[:\-]?\s*(\d{{4}}-\d{{2}}-\d{{2}})", text)
    return match.group(1) if match else None

def extract_emp_id(text):
    match = re.search(r'\bP\d{4,}\b', text)
    if match:
        return match.group(0)
    match = re.search(r'(รหัสพนักงาน|Emp|Employee ID)\s*[:\-]?\s*(P\d+)', text, re.IGNORECASE)
    if match:
        return match.group(2)
    lines = text.split("\n")
    for line in lines:
        if re.match(r'P\d{4,}', line.strip()):
            return line.strip()
    return 'unknown'

for page_num in range(pdf_document.page_count):
    page = pdf_document.load_page(page_num)
    page_height = page.rect.height

    top_half = page.get_text("text", clip=fitz.Rect(0, 0, page.rect.width, page_height / 2))
    bottom_half = page.get_text("text", clip=fitz.Rect(0, page_height / 2, page.rect.width, page_height))

    top_pdf_path = os.path.join(permanent_folder, f"top_{page_num + 1}.pdf")
    bottom_pdf_path = os.path.join(permanent_folder, f"bottom_{page_num + 1}.pdf")

    emp_id = extract_emp_id(top_half)
    emp_name = extract_text_value(top_half, "ชื่อพนักงาน")
    dep_name = extract_text_value(top_half, "แผนก")
    pay_date_start = extract_date(bottom_half, "วันที่เริ่ม")
    pay_date_end = extract_date(bottom_half, "วันที่สิ้นสุด")
    pay_date = extract_date(bottom_half, "วันที่จ่าย")
    account_no = extract_text_value(bottom_half, "เลขที่บัญชี")

    income_daily_rate = extract_value(bottom_half, "อัตรารายวัน")
    income_daily = extract_value(bottom_half, "รายได้รายวัน")
    income_holiday = extract_value(bottom_half, "รายได้วันหยุด")
    income_75 = extract_value(bottom_half, "รายได้ 75%")
    income_1_5 = extract_value(bottom_half, "รายได้ 1.5 เท่า")
    income_2 = extract_value(bottom_half, "รายได้ 2 เท่า")
    income_3 = extract_value(bottom_half, "รายได้ 3 เท่า")
    income_ot = extract_value(bottom_half, "รายได้ OT")
    income_bonus = extract_value(bottom_half, "โบนัส")
    income_position_allowance = extract_value(bottom_half, "เบี้ยตำแหน่ง")
    income_other_allowance = extract_value(bottom_half, "ค่าเบี้ยเลี้ยงอื่นๆ")
    income_total = extract_value(bottom_half, "รายได้รวม")

    deduction_ss = extract_value(bottom_half, "ประกันสังคม")
    deduction_advance = extract_value(bottom_half, "หักล่วงหน้า")
    deduction_uniform = extract_value(bottom_half, "หักชุดยูนิฟอร์ม")
    deduction_other = extract_value(bottom_half, "หักอื่นๆ")
    deduction_total = extract_value(bottom_half, "หักรวม")
    net_income = extract_value(bottom_half, "รายได้สุทธิ")

    if not emp_id or not emp_name:
        print(f"Missing essential data on page {page_num + 1}. Skipping...")
        continue

    try:
        sql = """
            INSERT INTO tb_payslips (
                emp_id, emp_name, dep_name, page_number, part, content, slip_divide,
                pay_date_start, pay_date_end, pay_date, account_no, income_daily_rate,
                income_daily, income_holiday, income_75, income_1_5, income_2, income_3,
                income_ot, income_bonus, income_position_allowance, income_other_allowance,
                income_total, deduction_ss, deduction_advance, deduction_uniform,
                deduction_other, deduction_total, net_income, uploaded_at
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
                      %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW())
        """
        cursor.execute(sql, (
            emp_id, emp_name, dep_name, page_num + 1, 'top', top_half, top_pdf_path,
            pay_date_start, pay_date_end, pay_date, account_no, income_daily_rate,
            income_daily, income_holiday, income_75, income_1_5, income_2, income_3,
            income_ot, income_bonus, income_position_allowance, income_other_allowance,
            income_total, deduction_ss, deduction_advance, deduction_uniform,
            deduction_other, deduction_total, net_income
        ))
        conn.commit()
    except mysql.connector.Error as err:
        print(f"Error inserting data: {err}")
        conn.rollback()

cursor.close()
conn.close()
pdf_document.close()

print("PDF data processed successfully.")
