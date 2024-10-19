const express = require('express');
const mysql = require('mysql2');
const fs = require('fs');
const pdfLib = require('pdf-lib');  // ใช้ pdf-lib หรือ pdf-parse ตามต้องการ
const pdfParse = require('pdf-parse');
const path = require('path');
const app = express();
const port = 3000;

// เชื่อมต่อกับฐานข้อมูล MySQL
const conn = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'salary_slip'
});

conn.connect((err) => {
    if (err) throw err;
    console.log('Connected to MySQL database');
});

// ใช้ express.json() เพื่อจัดการ request body
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// API สำหรับประมวลผล PDF
app.post('/process-pdf', async (req, res) => {
    const { filePath, pdfId } = req.body;
    
    if (!filePath || !pdfId) {
        return res.status(400).json({ message: 'Invalid parameters' });
    }

    try {
        const dataBuffer = fs.readFileSync(filePath);  // อ่านไฟล์ PDF
        const pdfData = await pdfParse(dataBuffer);
        const pageCount = pdfData.numpages;

        const permanentFolder = 'permanent_pdfs';
        if (!fs.existsSync(permanentFolder)) {
            fs.mkdirSync(permanentFolder);  // สร้างโฟลเดอร์ถาวรสำหรับจัดเก็บ PDF
        }

        // วนลูปเพื่อประมวลผลแต่ละหน้าใน PDF
        for (let pageNum = 0; pageNum < pageCount; pageNum++) {
            const page = pdfData.pages[pageNum];
            const pageText = page.text;
            const pageHeight = page.height;

            // แบ่งครึ่งบนและครึ่งล่างของหน้า
            const halfIndex = Math.floor(pageText.length / 2);
            const topHalf = pageText.substring(0, halfIndex);
            const bottomHalf = pageText.substring(halfIndex);

            // สร้างไฟล์ PDF สำหรับครึ่งบนและครึ่งล่าง
            const topPdfPath = path.join(permanentFolder, `top_${pageNum + 1}.pdf`);
            const bottomPdfPath = path.join(permanentFolder, `bottom_${pageNum + 1}.pdf`);

            // บันทึกครึ่งบนและครึ่งล่างลงในไฟล์ PDF
            fs.writeFileSync(topPdfPath, topHalf);
            fs.writeFileSync(bottomPdfPath, bottomHalf);

            // ฟังก์ชันสำหรับดึงข้อมูลพนักงาน
            const extractEmpInfo = (text) => {
                const empIdMatch = text.match(/\bP\d{4,}\b/);
                const empId = empIdMatch ? empIdMatch[0] : 'unknown';

                const empNameMatch = text.match(/P\d{4,}\s*(?:ชื่อสกุล|Name|Emp)?[:\-]?\s*([^\n]*)/i);
                const empName = empNameMatch ? empNameMatch[1].trim() : 'unknown';

                const empDepMatch = text.match(/Dep\.\s*(\S.*)/);
                const empDep = empDepMatch ? empDepMatch[1].trim() : 'unknown';

                return { empId, empName, empDep };
            };

            // ฟังก์ชันสำหรับดึงข้อมูลเงินเดือน
            const extractSalaryDetails = (text) => {
                const salaryIncomeMatch = text.match(/รวมเงินได้\s*[:\-]?\s*([\d,]+\.\d{2})/);
                const salaryIncome = salaryIncomeMatch ? parseFloat(salaryIncomeMatch[1].replace(',', '')) : 0.0;

                const salaryDeductionMatch = text.match(/รวมเงินหัก\s*[:\-]?\s*([\d,]+\.\d{2})/);
                const salaryDeduction = salaryDeductionMatch ? parseFloat(salaryDeductionMatch[1].replace(',', '')) : 0.0;

                const salaryNetMatch = text.match(/เงินได้สุทธิ\s*[:\-]?\s*([\d,]+\.\d{2})/);
                const salaryNet = salaryNetMatch ? parseFloat(salaryNetMatch[1].replace(',', '')) : 0.0;

                return { salaryIncome, salaryDeduction, salaryNet };
            };

            // ดึงข้อมูลจากครึ่งบน
            const { empId: empIdTop, empName: empNameTop, empDep: empDepTop } = extractEmpInfo(topHalf);
            const { salaryIncome: salaryIncomeTop, salaryDeduction: salaryDeductionTop, salaryNet: salaryNetTop } = extractSalaryDetails(topHalf);

            // ดึงข้อมูลจากครึ่งล่าง
            const { empId: empIdBottom, empName: empNameBottom, empDep: empDepBottom } = extractEmpInfo(bottomHalf);
            const { salaryIncome: salaryIncomeBottom, salaryDeduction: salaryDeductionBottom, salaryNet: salaryNetBottom } = extractSalaryDetails(bottomHalf);

            // บันทึกข้อมูลครึ่งบนในฐานข้อมูล
            const sqlTop = `
                INSERT INTO tb_payslips (emp_id, emp_name, emp_dep, salary_income, salary_deduction, salary_net, page_number, part, content, slip_divide)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            `;
            conn.query(sqlTop, [empIdTop, empNameTop, empDepTop, salaryIncomeTop, salaryDeductionTop, salaryNetTop, pageNum + 1, 'top', topHalf, topPdfPath]);

            // บันทึกข้อมูลครึ่งล่างในฐานข้อมูล
            const sqlBottom = `
                INSERT INTO tb_payslips (emp_id, emp_name, emp_dep, salary_income, salary_deduction, salary_net, page_number, part, content, slip_divide)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            `;
            conn.query(sqlBottom, [empIdBottom, empNameBottom, empDepBottom, salaryIncomeBottom, salaryDeductionBottom, salaryNetBottom, pageNum + 1, 'bottom', bottomHalf, bottomPdfPath]);
        }

        // ส่งผลลัพธ์กลับไปยังผู้เรียก
        res.json({ message: 'PDF processed and data inserted successfully', status: 'success' });
    } catch (error) {
        console.error('Error processing PDF:', error);
        res.status(500).json({ message: 'PDF processing failed', status: 'error' });
    }
});

// เริ่มต้นเซิร์ฟเวอร์
app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});
