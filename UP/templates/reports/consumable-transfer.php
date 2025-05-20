<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_admin();

$page_title = "Акт приема-передачи расходных материалов";
require_once __DIR__ . '/../../../models/Consumable.php';
require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../lib/fpdf/fpdf.php';

$db = (new Database())->connect();
$consumable = new Consumable($db);
$users = new User($db);

$consumables_list = $consumable->getAll();
$recipients_list = $users->getAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consumable_id = $_POST['consumable_id'];
    $quantity = $_POST['quantity'];
    $recipient_id = $_POST['recipient_id'];
    $comments = $_POST['comments'];
    
    // Получаем данные расходника
    $consumable->getById($consumable_id);
    if (!$consumable->id) {
        $error = "Расходный материал не найден";
    } elseif ($quantity > $consumable->quantity) {
        $error = "Недостаточное количество на складе";
    }
    
    // Получаем данные получателя
    $recipient = new User($db);
    $recipient->getById($recipient_id);
    if (!$recipient->id) {
        $error = "Получатель не найден";
    }
    
    if (!$error) {
        // Создаем PDF документ
        class PDF extends FPDF {
            function Header() {
                $this->SetFont('Arial','B',14);
                $this->Cell(0,10,'АКТ приема-передачи расходных материалов',0,1,'C');
                $this->Ln(5);
            }
            
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(0,10,'Страница '.$this->PageNo().'/{nb}',0,0,'C');
            }
        }
        
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);
        
        // Заголовок
        $pdf->Cell(0,10,'г. Пермь    ' . date('d.m.Y'),0,1,'R');
        $pdf->Ln(10);
        
        // Основной текст
        $pdf->MultiCell(0,10,'КГАПОУ Пермский Авиационный техникум им. А.Д. Швецова передает сотруднику ' . 
                          $recipient->last_name . ' ' . $recipient->first_name . ' ' . $recipient->middle_name . ', а сотрудник принимает от учебного учреждения следующие расходные материалы:',0,1);
        $pdf->Ln(5);
        
        // Таблица с расходниками
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(80,10,'Наименование',1,0,'C');
        $pdf->Cell(40,10,'Тип',1,0,'C');
        $pdf->Cell(30,10,'Количество',1,0,'C');
        $pdf->Cell(40,10,'Дата поступления',1,1,'C');
        
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(80,10,$consumable->name,1,0,'L');
        $pdf->Cell(40,10,$consumable->type_name ?? '-',1,0,'C');
        $pdf->Cell(30,10,$quantity,1,0,'C');
        $pdf->Cell(40,10,$consumable->receipt_date ? date('d.m.Y', strtotime($consumable->receipt_date)) : '-',1,1,'C');
        $pdf->Ln(10);
        
        // Подписи
        $pdf->Cell(90,10,'Передал: ___________________',0,0,'L');
        $pdf->Cell(90,10,'Принял: ___________________',0,1,'L');
        $pdf->Cell(90,10,'(подпись)',0,0,'L');
        $pdf->Cell(90,10,'(подпись)',0,1,'L');
        $pdf->Ln(10);
        
        // Комментарий
        if ($comments) {
            $pdf->SetFont('Arial','I',10);
            $pdf->MultiCell(0,10,'Примечание: ' . $comments,0,1);
        }
        
        // Обновляем количество расходников в базе
        $consumable->quantity -= $quantity;
        $consumable->update();
        
        // Вывод PDF
        $pdf->Output('D', 'Акт приема-передачи расходных материалов ' . date('Y-m-d') . '.pdf');
        exit();
    }
}
?>

<div class="content-header">
    <h1 class="content-title">Акт приема-передачи расходных материалов</h1>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-text"></i> Формирование акта
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="consumable_id" class="form-label required-field">Расходный материал</label>
                        <select class="form-select" id="consumable_id" name="consumable_id" required>
                            <option value="">-- Выберите расходный материал --</option>
                            <?php while($row = $consumables_list->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $row['id'] ?>" <?= isset($_POST['consumable_id']) && $_POST['consumable_id'] == $row['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['name']) ?> (<?= $row['quantity'] ?> шт.)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="recipient_id" class="form-label required-field">Получатель</label>
                        <select class="form-select" id="recipient_id" name="recipient_id" required>
                            <option value="">-- Выберите получателя --</option>
                            <?php while($row = $recipients_list->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $row['id'] ?>" <?= isset($_POST['recipient_id']) && $_POST['recipient_id'] == $row['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['last_name']) ?> <?= htmlspecialchars($row['first_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="quantity" class="form-label required-field">Количество</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required 
                               value="<?= $_POST['quantity'] ?? 1 ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="comments" class="form-label">Комментарий</label>
                <textarea class="form-control" id="comments" name="comments" rows="3"><?= $_POST['comments'] ?? '' ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-download"></i> Скачать акт
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>