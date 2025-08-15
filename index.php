<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'ukk2025_todolist');

// Variabel awal
$task = '';
$priority = '';
$due_date = '';
$isUpdate = false;
$edit_id = 0;

// Proses tambah
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO tasks (tasks, priority, due_date, status) VALUES ('$task', '$priority', '$due_date', '0')");
        echo "<script>alert('Data Berhasil Disimpan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Semua kolom harus diisi');</script>";
    }
}

// Proses edit - ambil data
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $isUpdate = true;
    $result_edit = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id = $edit_id");
    $row_edit = mysqli_fetch_assoc($result_edit);
    $task = $row_edit['tasks'];
    $priority = $row_edit['priority'];
    $due_date = $row_edit['due_date'];
}

// Simpan hasil edit
if (isset($_POST['update_task'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    mysqli_query($koneksi, "UPDATE tasks SET tasks='$task', priority='$priority', due_date='$due_date' WHERE id=$id");
    echo "<script>alert('Data berhasil diupdate'); window.location='index.php';</script>";
}

// Tandai selesai
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE tasks SET status = 1 WHERE id = $id");
    echo "<script>alert('Data Berhasil Diperbarui'); window.location='index.php';</script>";
    exit;
}

// Hapus task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id = $id");
    echo "<script>alert('Data berhasil dihapus'); window.location='index.php';</script>";
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM tasks ORDER BY status ASC, priority DESC, due_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Todo List | UKK RPL 2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #83a4d4, #b6fbff);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        h2 {
            font-weight: bold;
            color: #333;
            text-shadow: 1px 1px 1px #fff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Aplikasi ToDo List</h2>
    <form method="POST" class="border rounded bg-light p-2">
        <input type="hidden" name="id" value="<?= $edit_id ?>">
        <label class="form-label">Nama Task</label>
        <input type="text" name="task" class="form-control" value="<?= htmlspecialchars($task) ?>" placeholder="Masukkan Task Baru" autocomplete="off" required>

        <label class="form-label">Prioritas</label>
        <select name="priority" class="form-control" required>
            <option value="">-- Pilih Prioritas --</option>
            <option value="1" <?= ($priority == '1') ? 'selected' : '' ?>>Low</option>
            <option value="2" <?= ($priority == '2') ? 'selected' : '' ?>>Medium</option>
            <option value="3" <?= ($priority == '3') ? 'selected' : '' ?>>High</option>
        </select>

        <label class="form-label">Tanggal</label>
        <input type="date" name="due_date" class="form-control" value="<?= $due_date ?: date('Y-m-d') ?>" required>

        <?php if ($isUpdate): ?>
            <button type="submit" name="update_task" class="btn btn-warning mt-2">Update</button>
            <a href="index.php" class="btn btn-secondary mt-2">Batal</a>
        <?php else: ?>
            <button type="submit" name="add_task" class="btn btn-primary mt-2">Tambah</button>
        <?php endif; ?>
    </form>

    <hr>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>No</th>
            <th>Task</th>
            <th>Prioritas</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['tasks']) ?></td>
                    <td>
                        <?php
                        switch ($row['priority']) {
                            case 1: echo "Low"; break;
                            case 2: echo "Medium"; break;
                            case 3: echo "High"; break;
                        }
                        ?>
                    </td>
                    <td><?= $row['due_date'] ?></td>
                    <td><?= $row['status'] == 0 ? "Belum selesai" : "Selesai" ?></td>
                    <td>
                        <?php if ($row['status'] == 0): ?>
                            <a href="?complete=<?= $row['id'] ?>" class="btn btn-success btn-sm">Selesai</a>
                        <?php endif; ?>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php }
        } else {
            echo '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
