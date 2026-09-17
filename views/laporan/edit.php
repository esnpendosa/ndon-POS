<?php
    // [SECURITY] Hanya Administrator yang berhak mengedit / merevisi transaksi
    if (empty($_SESSION['codekop_session']) || (int)$_SESSION['codekop_session']['akses'] !== 1) {
        set_flashdata("Gagal", "Akses ditolak. Hanya Administrator yang dapat mengedit transaksi !", "danger");
        redirect('index.php');
        exit;
    }

    $id =  getGet("id",true);
    $sql = "SELECT users.name, pelanggan.nama_pelanggan, penjualan.* 
            FROM penjualan 
            LEFT JOIN users 
            ON penjualan.id_member = users.id 
            LEFT JOIN pelanggan 
            ON penjualan.id_pelanggan=pelanggan.id WHERE no_trx = ?";
    $row = $connectdb->prepare($sql);
    $row->execute(array($id));
    $edit = $row->fetch(PDO::FETCH_OBJ);
    if(!$edit) {
        redirect('index.php');
        exit;
    }
?>
<?php if(!empty(flashdata())){ echo flashdata(); }?>
<div class="row">
    <div class="col-md-4">
        <a class="btn btn-danger btn-md" href="<?= 'index.php';?>" role="button">
            <i class="fas fa-angle-left mr-1"></i> Kembali
        </a>
        <a href="javascript:void(0);" id="cetak_struk" class="btn btn-primary">
            <i class="fas fa-print mr-1"></i> Print
        </a>
        <div class="card mt-3">
            <div class="card-header">
                Detail Transaksi
            </div>
            <div class="card-body">
                <form method="post" action="proses.php?aksi=editlap">
                    <div class="row">
                        <div class="col-sm-4">No trx</div>
                        <div class="col-sm-8"><?= $edit->no_trx;?></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-4">ID Kasir</div>
                        <div class="col-sm-8"><?= $edit->name;?></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-4">ID pelanggan</div>
                        <div class="col-sm-8"><?= $edit->nama_pelanggan ?? '-';?></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-4">Jumlah</div>
                        <div class="col-sm-8"><?= $edit->jumlah;?></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-4">Total Penjualan</div>
                        <div class="col-sm-8"><?= getRupiah($edit->total,'Rp');?></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4">Metode Bayar</div>
                        <div class="col-sm-8"><?= ucwords($edit->metode_bayar)?></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4">Dibayar</div>
                        <div class="col-sm-8"><input type="number" class="form-control" value="<?= $edit->bayar;?>"
                                name="dibayar"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4">Kembali</div>
                        <div class="col-sm-8"><?= getRupiah($edit->bayar - $edit->total,'Rp');?></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-4">Tanggal input</div>
                        <div class="col-sm-8"><?= $edit->tanggal_input;?></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4">Created at</div>
                        <div class="col-sm-8"><?= $edit->created_at;?></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4">Aksi</div>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control" value="<?= $edit->total;?>"
                                name="total">
                            <input type="hidden" class="form-control" value="<?= $edit->no_trx;?>"
                                name="notrx">
                            <button type="submit" class="btn btn-primary btn-md">Simpan</button>    
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <button type="button" class="btn btn-primary btn-md mt-1 mb-2" data-toggle="modal" data-target="#modelId">
            <i class="fa fa-search mr-1"></i> Daftar Barang
        </button>
        <div class="card mt-3">
            <div class="card-header">
                Daftar Transaksi
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm w-100">
                        <tr>
                            <td><b>No</b></td>
                            <td><b>Nama</b></td>
                            <td><b>Harga</b></td>
                            <td><b>Qty</b></td>
                            <td><b>Diskon</b></td>
                            <td><b>Total</b></td>
                            <td><b>Aksi</b></td>
                        </tr>
                        <?php 
                            $no =1;
                            $diskon = 0;
                            $sql = "SELECT * FROM penjualan_detail 
                                WHERE no_trx = ?
                                ORDER BY id ASC";
                            $row = $connectdb->prepare($sql);
                            $row->execute(array($id));
                            $hasil = $row->fetchAll(PDO::FETCH_OBJ);
                            foreach($hasil as $r){
                        ?>
                        <tr>
                            <form method="post" action="proses.php?aksi=editstok">
                                <td><?=$no;?></td>
                                <td><?=$r->nama_barang;?></td>
                                <td><?= getRupiah($r->jual);?> x </td>
                                <td><input type="number" style="width:100px" value="<?=$r->qty;?>" class="form-control" name="qty"></td>
                                <td><input type="number" style="width:150px" value="<?=$r->diskon;?>" class="form-control" name="diskon">
                                </td>
                                <td><?= getRupiah($r->total,'Rp');?></td>
                                <td>
                                    <input type="hidden" name="id" value="<?= $r->id;?>">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        <a href="proses.php?aksi=deletestok&id=<?= $r->id;?>" class="btn btn-danger btn-sm"
                                            onclick="javascript: return confirm('Apakah barang yang telah terjual akan dihapus ?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php $no++; $diskon += $r->diskon;}?>
                        <tr>
                            <th colspan="3">Total</th>
                            <th><?= $edit->jumlah;?></th>
                            <th><?= getRupiah($diskon);?></th>
                            <th><?= getRupiah($edit->total);?></th>
                            <th>#</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Barang -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-search mr-1"></i> Daftar Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Light table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered w-100" id="table-artikel-query">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Barang</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function mobilecheck() {
    var check = false;
    (function(a) {
        if (
            /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i
            .test(a) ||
            /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i
            .test(a.substr(0, 4))) check = true;
    })(navigator.userAgent || navigator.vendor || window.opera);
    return check;
}

$(document).ready(function() {
    var isMobile = mobilecheck();
    $("#cetak_struk").click(function() {
        $.ajax({
            url: "<?= "../helper/cetak/cetak.php?no=".$id;?>",
            type: 'GET',
            dataType: 'html',
            success: function(html) {
                if (isMobile) {
                    var w = window.open(window.location.href, "_blank");
                    w.document.open();
                    w.document.write(html);
                    w.document.close();
                    w.print();
                } else {
                    var iframe = document.createElement('iframe');
                    iframe.style.position = 'absolute';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = 'none';
                    document.body.appendChild(iframe);
                    var doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    doc.write(html);
                    doc.close();

                    var printWindow = iframe.contentWindow || iframe;
                    printWindow.focus();
                    printWindow.print();

                    // Monitor when the print dialog is closed
                    printWindow.onafterprint = function() {
                        document.body.removeChild(iframe);
                    };

                    // Fallback to remove iframe after a delay
                    setTimeout(function() {
                        if (document.body.contains(iframe)) {
                            document.body.removeChild(iframe);
                        }
                    }, 5000);
                }
            },
            error: function(xmlhttprequest, textstatus, message) {
                if (textstatus === "timeout") {
                    alert("request timeout");
                } else {
                    alert("request timeout");
                }
            }
        });
    });
});
</script>
<script>
var tabel = null;
$(document).ready(function() {
    tabel = $('#table-artikel-query').DataTable({
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "ordering": true, // Set true agar bisa di sorting
        "order": [
            [0, 'DESC']
        ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
        "ajax": {
            <?php if(isset($_GET['stok'])){?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang&stok=yes';?>", // URL file untuk proses select datanya
            <?php }else{?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang';?>", // URL file untuk proses select datanya
            <?php }?> "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [10, 25, 50],
            [10, 25, 50]
        ], // Combobox Limit
        "columns": [{
                "data": 'id',
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "id_barang"
            },
            {
                "data": "gambar",
                "render": function(data, type, row, meta) {
                    if (row.gambar !== null) {
                        return `<center>
                                        <a href="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" data-toggle="lightbox" 
                                            data-title="${row.nama_barang}" data-gallery="gallery">
                                            <img src="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" 
                                                alt="${row.nama_barang}" class="img-fluid" width="80"/>
                                        </a>
                                    </center>`;
                    } else {
                        return `<center>
                                        <a href="<?= $baseURL;?>assets/dist/img/no-image.jpg" data-toggle="lightbox" 
                                            data-title="${row.nama_barang}" data-gallery="gallery">
                                            <img src="<?= $baseURL;?>assets/dist/img/no-image.jpg" 
                                                alt="${row.nama_barang}" class="img-fluid" width="80"/>
                                        </a>
                                    </center>`;
                    }
                }
            },
            {
                "data": "nama_barang"
            },
            {
                "data": "nama_kategori"
            },
            {
                "data": "merk"
            },
            {
                data: 'harga_jual',
                render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp')
            },
            {
                "data": "satuan_barang"
            },
            {
                "data": "stok"
            },
            {
                "data": "id",
                "render": function(data, type, row, meta) {
                    return `<form method="post" action="proses.php?aksi=addbarang">   
                                    <input type="number" value="1"
                                        class="form-control" 
                                        name="qty" id="qty" placeholder="">
                                    <input type="hidden" value="${row.id}"
                                        class="form-control" 
                                        name="id" id="id" placeholder="">
                                    <input type="hidden" value="<?= $edit->no_trx;?>"
                                        class="form-control" 
                                        name="no_trx" id="no_trx" placeholder="">
                                    <button type="submit" 
                                        class="btn btn-success btn-sm mt-2 mb-2 btn-block" title="Tambahkan">
                                        <i class="fa fa-plus mr-1"></i>
                                    </button> 
                                </form>`;
                }
            },
        ],
    });
});
</script>