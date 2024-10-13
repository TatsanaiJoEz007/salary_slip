<?php
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdfFile'])) {
    $pdfFile = $_FILES['pdfFile'];

    if ($pdfFile['type'] == 'application/pdf') {
        $pdfData = file_get_contents($pdfFile['tmp_name']);
        $pdfName = $pdfFile['name'];

        $stmt = $conn->prepare("INSERT INTO tb_pdf_files (pdf_name, pdf_data) VALUES (?, ?)");
        if ($stmt) {
            $null = NULL;
            $stmt->bind_param("sb", $pdfName, $null);
            $stmt->send_long_data(1, $pdfData);

            if ($stmt->execute()) {
                $pdf_id = $stmt->insert_id;
                $uploaded_file_path = 'uploads/' . $pdfName;
                file_put_contents($uploaded_file_path, $pdfData);

                $python_script_path = 'process_pdf.py';
                $command = escapeshellcmd("python $python_script_path $uploaded_file_path $pdf_id");
                $output = shell_exec($command);

                if ($output) {
                    echo "<script>
                        window.location.href = '../upload_page.php?status=success&message=PDF uploaded and processed successfully';
                    </script>";
                } else {
                    echo "<script>
                        window.location.href = '../upload_page.php?status=error&message=PDF uploaded but processing failed';
                    </script>";
                }
            } else {
                echo "<script>
                    window.location.href = '../upload_page.php?status=error&message=Error uploading PDF: {$stmt->error}';
                </script>";
            }
            $stmt->close();
        } else {
            echo "<script>
                window.location.href = '../upload_page.php?status=error&message=Failed to prepare SQL statement: {$conn->error}';
            </script>";
        }
    } else {
        echo "<script>
            window.location.href = '../upload_page.php?status=error&message=Only PDF files are allowed.';
        </script>";
    }
} else {
    echo "<script>
        window.location.href = '../upload_page.php?status=error&message=Invalid request';
    </script>";
}

$conn->close();
?>
