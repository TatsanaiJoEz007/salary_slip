#!/usr/bin/env python3

import os
import sys
import logging

# ตั้งค่า logging เพื่อบันทึกผลลัพธ์และข้อผิดพลาด
logging.basicConfig(
    filename='/var/www/html/admin/function/delete_pdfs.log',
    level=logging.INFO,
    format='%(asctime)s:%(levelname)s:%(message)s'
)

def delete_files(directory):
    try:
        # ตรวจสอบว่า directory มีอยู่จริง
        if not os.path.isdir(directory):
            logging.error(f"Directory ไม่พบ: {directory}")
            sys.exit(1)
        
        # ลบไฟล์ทั้งหมดใน directory
        for filename in os.listdir(directory):
            file_path = os.path.join(directory, filename)
            try:
                if os.path.isfile(file_path):
                    os.remove(file_path)
                    logging.info(f"ลบไฟล์สำเร็จ: {file_path}")
                elif os.path.isdir(file_path):
                    # หากมีโฟลเดอร์ย่อย ให้ลบทั้งหมดภายใน
                    os.rmdir(file_path)
                    logging.info(f"ลบโฟลเดอร์สำเร็จ: {file_path}")
            except Exception as e:
                logging.error(f"ไม่สามารถลบไฟล์ {file_path}: {e}")
        
        logging.info("ลบไฟล์ทั้งหมดเรียบร้อยแล้ว")
    
    except Exception as e:
        logging.error(f"เกิดข้อผิดพลาดในการลบไฟล์: {e}")
        sys.exit(1)

if __name__ == "__main__":
    # ระบุ path ของ directory ที่ต้องการลบไฟล์
    target_directory = "/var/www/html/admin/function/permanent_pdfs"
    delete_files(target_directory)
