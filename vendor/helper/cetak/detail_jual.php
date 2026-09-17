<?php
    session_start();
    include '../../setting.php';
    include '../../helper.php';
    if(!empty($_SESSION['codekop_session'])) {
        $uid =  (int)$_SESSION['codekop_session']['id'];
        $sql_users = "SELECT * FROM users WHERE id = ?";
        $row_users = $connectdb->prepare($sql_users);
        $row_users->execute(array($uid));
        $users = $row_users->fetch(PDO::FETCH_OBJ);
    } else {
        redirect($baseURL.'login.php');
    }

    $id =  strip_tags(getGet("no", true));
    $sql = "SELECT users.name, pelanggan.nama_pelanggan, penjualan.* 
                FROM penjualan 
                LEFT JOIN users 
                ON penjualan.id_member = users.id 
                LEFT JOIN pelanggan 
                ON penjualan.id_pelanggan=pelanggan.id
                WHERE penjualan.no_trx = ?";
    $row = $connectdb->prepare($sql);
    $row->execute(array($id));
    $edit = $row->fetch(PDO::FETCH_OBJ);
    if(empty($edit)) {
        redirect($baseURL.'index.php');
    }

    $sql_toko =  "SELECT * FROM toko WHERE id = 1";
    $row_toko = $connectdb->prepare($sql_toko);
    $row_toko->execute();
    $toko = $row_toko->fetch(PDO::FETCH_OBJ);
?>
<div class="modal-body">
    <center>
        <b> <?= $toko->nama_toko;?></b><br>
        <?= $toko->alamat_toko;?>
        <br>
        Telp. <?= $toko->tlp;?>
    </center>
    <div class="clearfix mt-3"></div>
    <div class="table-responsive">
        <table class="w-100">
            <tr>
                <td>
                    TRX
                </td>
                <td>
                    :
                </td>
                <td>
                    <?= $edit->no_trx;?>
                </td>
            </tr>
            <tr>
                <td>
                    Kasir
                </td>
                <td>
                    :
                </td>
                <td>
                    <?= $edit->name;?>
                </td>
            </tr>
            <!-- <tr>
                    <td>
                        Pelanggan
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        <?= $edit->nama_pelanggan ?? '-';?>
                    </td>
                </tr> -->
            <tr>
                <td>
                    Tanggal
                </td>
                <td>
                    :
                </td>
                <td>
                    <?= $edit->created_at ?? '-';?>
                </td>
            </tr>
        </table>
    </div>
    <div class="clearfix mt-3"></div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm w-100">
            <tr>
                <td><b>Nama</b></td>
                <td><b>Harga</b></td>
                <td><b>Qty</b></td>
                <td><b>Diskon</b></td>
                <td><b>Total</b></td>
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
                foreach($hasil as $r) {
            ?>
            <tr>
                <td><?=$r->nama_barang;?></td>
                <td><?= getRupiah($r->jual);?> x </td>
                <td><?=$r->qty;?></td>
                <td><?= getRupiah($r->diskon,'Rp');?></td>
                <td><?= getRupiah($r->total,'Rp');?></td>
            </tr>
            <?php $no++; $diskon += $r->diskon; }?>
        </table>
        <div class="doted"></div>
        <table>
            <tr>
                <td><b>Total Diskon</b></td>
                <td>:</td>
                <td><?= getRupiah($diskon ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td><b>Total Bayar</b></td>
                <td>:</td>
                <td><?= getRupiah($edit->total ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td><b>Metode Bayar</b></td>
                <td>:</td>
                <td><?= ucwords($edit->metode_bayar) ?></td>
            </tr>
            <tr>
                <td><b>Dibayar</b></td>
                <td>:</td>
                <td><?= getRupiah($edit->bayar ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td><b>Kembali</b></td>
                <td>:</td>
                <td><?= getRupiah(($edit->bayar-$edit->total) ?? 0,'Rp');?></td>
            </tr>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <a href="javascript:void(0);" id="cetak_struk" class="btn btn-primary">
        <i class="fas fa-print mr-1"></i> Print
    </a>
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