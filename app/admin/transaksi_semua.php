<?php
include 'header.php';
include '../koneksi.php';

// Ambil daftar bank
$banks = mysqli_query($koneksi, "SELECT * FROM bank ORDER BY bank_nama ASC");

// Tangkap parameter bank_id dari URL (GET), default bank pertama
if (isset($_GET['bank_id'])) {
    $selected_bank_id = intval($_GET['bank_id']);
} else {
    $first_bank = mysqli_fetch_assoc($banks);
    $selected_bank_id = $first_bank['bank_id'];
    mysqli_data_seek($banks, 0); // reset pointer agar bisa di-loop ulang
}

// Ambil nama bank yang dipilih
$bank_name = '';
$result_bank = mysqli_query($koneksi, "SELECT bank_nama FROM bank WHERE bank_id = $selected_bank_id");
if ($row_bank = mysqli_fetch_assoc($result_bank)) {
    $bank_name = $row_bank['bank_nama'];
}
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Transaksi
      <small>Data Transaksi Bank <?php echo htmlspecialchars($bank_name); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">

        <!-- Form pilih bank -->
        <form method="get" class="form-inline" style="margin-bottom:15px;">
          <label for="bank_id">Pilih Bank: </label>
          <select name="bank_id" id="bank_id" class="form-control" onchange="this.form.submit()">
            <?php while ($b = mysqli_fetch_assoc($banks)) { ?>
              <option value="<?php echo $b['bank_id']; ?>" <?php if ($b['bank_id'] == $selected_bank_id) echo 'selected'; ?>>
                <?php echo htmlspecialchars($b['bank_nama']); ?>
              </option>
            <?php } ?>
          </select>
          &nbsp;&nbsp;
          <a href="export_pdf.php?bank_id=<?php echo $selected_bank_id; ?>" target="_blank" class="btn btn-danger">
            Export PDF
          </a>
        </form>

        <div class="box box-info">

          <div class="box-header">
            <h3 class="box-title">Transaksi Bank <?php echo htmlspecialchars($bank_name); ?></h3>
            <div class="btn-group pull-right">
              <p><b>Total Saldo:</b> 
                <?php
                $saldo_query = mysqli_query($koneksi, "SELECT bank_saldo FROM bank WHERE bank_id = $selected_bank_id");
                $saldo_data = mysqli_fetch_assoc($saldo_query);
                echo "Rp. " . number_format($saldo_data['bank_saldo']) . " ,-";
                ?>
              </p>
            </div>
          </div>

          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%" rowspan="2">NO</th>
                    <th width="10%" rowspan="2" class="text-center">TANGGAL</th>
                    <th rowspan="2" class="text-center">KATEGORI</th>
                    <th colspan="2" class="text-center">JENIS</th>
                  </tr>
                  <tr>
                    <th class="text-center">PEMASUKAN</th>
                    <th class="text-center">PENGELUARAN</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $data = mysqli_query($koneksi, "SELECT t.*, k.kategori FROM transaksi t JOIN kategori k ON k.kategori_id = t.transaksi_kategori WHERE transaksi_bank = $selected_bank_id ORDER BY transaksi_id DESC");
                  while ($d = mysqli_fetch_assoc($data)) {
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no++; ?></td>
                      <td class="text-center"><?php echo date('d-m-Y', strtotime($d['transaksi_tanggal'])); ?></td>
                      <td><?php echo htmlspecialchars($d['kategori']); ?></td>
                      <td class="text-center">
                        <?php
                        if ($d['transaksi_jenis'] == "Pemasukan") {
                          echo "Rp. " . number_format($d['transaksi_nominal']) . " ,-";
                        } else {
                          echo "-";
                        }
                        ?>
                      </td>
                      <td class="text-center">
                        <?php
                        if ($d['transaksi_jenis'] == "Pengeluaran") {
                          echo "Rp. " . number_format($d['transaksi_nominal']) . " ,-";
                        } else {
                          echo "-";
                        }
                        ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>

<?php include 'footer.php'; ?>

