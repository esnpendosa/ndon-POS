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

    // Ambil detail item transaksi
    $sql_items = "SELECT * FROM penjualan_detail WHERE no_trx = ? ORDER BY id ASC";
    $row_items = $connectdb->prepare($sql_items);
    $row_items->execute(array($id));
    $hasil = $row_items->fetchAll(PDO::FETCH_OBJ);

    $diskon = 0;
    $items_json = [];
    foreach($hasil as $r) {
        $diskon += (float)$r->diskon;
        $items_json[] = [
            'nama'   => $r->nama_barang,
            'qty'    => (int)$r->qty,
            'harga'  => (float)$r->jual,
            'diskon' => (float)$r->diskon,
            'total'  => (float)$r->total
        ];
    }
?>
<div class="modal-body p-3">
    <div id="printAreaStruk" style="font-family: monospace, 'Courier New', Courier, sans-serif; font-size: 11px;">
        <center>
            <b style="font-size: 13px;"><?= htmlspecialchars($toko->nama_toko);?></b><br>
            <?= htmlspecialchars($toko->alamat_toko);?><br>
            Telp. <?= htmlspecialchars($toko->tlp);?>
        </center>
        <div style="border-bottom: 1px dashed #444; margin: 6px 0;"></div>
        <table class="w-100" style="font-size: 11px;">
            <tr>
                <td style="width: 25%;">TRX</td>
                <td style="width: 5%;">:</td>
                <td><b><?= htmlspecialchars($edit->no_trx);?></b></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td><?= htmlspecialchars($edit->name);?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?= htmlspecialchars($edit->created_at ?? '-');?></td>
            </tr>
        </table>
        <div style="border-bottom: 1px dashed #444; margin: 6px 0;"></div>
        <table class="table table-bordered table-sm w-100" style="font-size: 11px; margin-bottom: 6px;">
            <thead class="thead-light">
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th class="text-center">Qty</th>
                    <th>Diskon</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hasil as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r->nama_barang);?></td>
                    <td><?= getRupiah($r->jual);?></td>
                    <td class="text-center"><?=$r->qty;?></td>
                    <td><?= getRupiah($r->diskon,'Rp');?></td>
                    <td class="text-right"><?= getRupiah($r->total,'Rp');?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="border-bottom: 1px dashed #444; margin: 6px 0;"></div>
        <table class="w-100" style="font-size: 11px;">
            <tr>
                <td>Total Diskon</td>
                <td style="width: 5%;">:</td>
                <td class="text-right"><?= getRupiah($diskon ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td><b>Total Bayar</b></td>
                <td>:</td>
                <td class="text-right"><b><?= getRupiah($edit->total ?? 0,'Rp');?></b></td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td>:</td>
                <td class="text-right"><?= ucwords($edit->metode_bayar) ?></td>
            </tr>
            <tr>
                <td>Dibayar</td>
                <td>:</td>
                <td class="text-right"><?= getRupiah($edit->bayar ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td>:</td>
                <td class="text-right"><b><?= getRupiah(($edit->bayar-$edit->total) ?? 0,'Rp');?></b></td>
            </tr>
        </table>
        <div style="border-bottom: 1px dashed #444; margin: 6px 0;"></div>
        <center style="font-size: 10px; margin-top: 4px;">
            Catatan: Barang yang telah dibeli tidak dapat ditukar/dikembalikan.<br>
            <b>TERIMA KASIH ATAS KUNJUNGAN ANDA</b>
        </center>
    </div>
</div>

<div class="modal-footer d-flex flex-column align-items-stretch" style="background-color: #f8f9fa;">
    <div class="d-flex justify-content-between align-items-center mb-2 w-100" style="font-size: 12px;">
        <span class="text-muted font-weight-bold">Ukuran Kertas:</span>
        <select id="modalPaperSize" class="form-control form-control-sm" style="width: 140px;">
            <option value="58" selected>58 mm (Standar)</option>
            <option value="80">80 mm (Besar)</option>
        </select>
    </div>

    <div class="btn-group btn-block mb-1" role="group">
        <button type="button" class="btn btn-success font-weight-bold" onclick="modalPrintBluetooth()">
            <i class="fas fa-print mr-1"></i> Cetak Bluetooth
        </button>
        <button type="button" class="btn btn-primary font-weight-bold" onclick="modalPrintStandard()">
            <i class="fas fa-desktop mr-1"></i> Cetak Biasa
        </button>
        <button type="button" class="btn btn-info font-weight-bold" onclick="modalPrintRawBT()">
            <i class="fas fa-mobile-alt mr-1"></i> RawBT
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-1 w-100">
        <span id="modal-bt-status" class="text-muted" style="font-size: 11.5px;">Siap mencetak</span>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i> Tutup
        </button>
    </div>
</div>

<script>
    const modalTrxData = {
        tokoNama: <?= json_encode($toko->nama_toko ?? 'POS Kasir'); ?>,
        tokoAlamat: <?= json_encode($toko->alamat_toko ?? ''); ?>,
        tokoTelp: <?= json_encode($toko->tlp ?? ''); ?>,
        noTrx: <?= json_encode($edit->no_trx ?? ''); ?>,
        kasir: <?= json_encode($edit->name ?? '-'); ?>,
        pelanggan: <?= json_encode($edit->nama_pelanggan ?? '-'); ?>,
        tanggal: <?= json_encode($edit->created_at ?? date('Y-m-d H:i:s')); ?>,
        metodeBayar: <?= json_encode(ucwords($edit->metode_bayar ?? 'Tunai')); ?>,
        diskon: <?= (float)$diskon; ?>,
        total: <?= (float)($edit->total ?? 0); ?>,
        bayar: <?= (float)($edit->bayar ?? 0); ?>,
        kembali: <?= (float)(($edit->bayar - $edit->total) ?? 0); ?>,
        items: <?= json_encode($items_json); ?>
    };

    function modalFormatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function setModalStatus(msg) {
        const el = document.getElementById('modal-bt-status');
        if (el) el.innerText = msg;
    }

    function buildModalEscPos(widthCols = 32) {
        const ESC = '\x1B';
        const GS  = '\x1D';

        let out = '';
        out += ESC + '@'; // Init
        out += ESC + 'a' + '\x01'; // Center
        out += ESC + 'E' + '\x01'; // Bold ON
        out += ESC + '!' + '\x20'; // Double height
        out += modalTrxData.tokoNama + '\n';
        out += ESC + '!' + '\x00'; // Normal
        out += ESC + 'E' + '\x00'; // Bold OFF
        if (modalTrxData.tokoAlamat) out += modalTrxData.tokoAlamat + '\n';
        if (modalTrxData.tokoTelp)   out += 'Telp: ' + modalTrxData.tokoTelp + '\n';

        out += '-'.repeat(widthCols) + '\n';
        out += ESC + 'a' + '\x00'; // Left
        out += padModalRow('TRX', ': ' + modalTrxData.noTrx, widthCols) + '\n';
        out += padModalRow('Kasir', ': ' + modalTrxData.kasir, widthCols) + '\n';
        out += padModalRow('Tanggal', ': ' + modalTrxData.tanggal, widthCols) + '\n';
        out += '-'.repeat(widthCols) + '\n';

        modalTrxData.items.forEach(item => {
            out += item.nama + '\n';
            let leftCol = modalFormatRupiah(item.harga) + ' x ' + item.qty;
            if (item.diskon > 0) {
                leftCol += ' (D:' + modalFormatRupiah(item.diskon) + ')';
            }
            let rightCol = modalFormatRupiah(item.total);
            out += padModalRow(leftCol, rightCol, widthCols) + '\n';
        });

        out += '-'.repeat(widthCols) + '\n';
        out += padModalRow('Metode Bayar', ': ' + modalTrxData.metodeBayar, widthCols) + '\n';
        if (modalTrxData.diskon > 0) {
            out += padModalRow('Total Diskon', ': ' + modalFormatRupiah(modalTrxData.diskon), widthCols) + '\n';
        }
        out += padModalRow('Total Tagihan', ': ' + modalFormatRupiah(modalTrxData.total), widthCols) + '\n';
        out += padModalRow('Dibayar', ': ' + modalFormatRupiah(modalTrxData.bayar), widthCols) + '\n';
        out += padModalRow('Kembalian', ': ' + modalFormatRupiah(modalTrxData.kembali), widthCols) + '\n';
        out += '-'.repeat(widthCols) + '\n';

        out += ESC + 'a' + '\x01'; // Center
        out += 'Barang yg dibeli tdk dpt ditukar\n';
        out += ESC + 'E' + '\x01';
        out += 'TERIMA KASIH\nATAS KUNJUNGAN ANDA\n';
        out += ESC + 'E' + '\x00';
        out += '\n\n\n';
        out += GS + 'V' + '\x42' + '\x00'; // Cut

        return new TextEncoder().encode(out);
    }

    function padModalRow(left, right, maxLen) {
        left = String(left || '');
        right = String(right || '');
        const spaceCount = maxLen - left.length - right.length;
        if (spaceCount > 0) {
            return left + ' '.repeat(spaceCount) + right;
        }
        return left + ' ' + right;
    }

    // 1. Cetak Bluetooth langsung dari modal
    async function modalPrintBluetooth() {
        if (!navigator.bluetooth) {
            alert('Browser Anda belum mendukung Web Bluetooth secara langsung.\nGunakan Google Chrome di Android / Laptop, atau gunakan tombol RawBT.');
            return;
        }

        try {
            setModalStatus('Mencari printer Bluetooth...');
            const paperSelect = document.getElementById('modalPaperSize');
            const paperWidth = (paperSelect && paperSelect.value === '80') ? 48 : 32;
            const printBytes = buildModalEscPos(paperWidth);

            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    '0000ffe0-0000-1000-8000-00805f9b34fb',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2'
                ]
            });

            setModalStatus('Menghubungkan ke ' + device.name + '...');
            const server = await device.gatt.connect();

            setModalStatus('Mencari layanan cetak...');
            const services = await server.getPrimaryServices();
            let writeCharacteristic = null;

            for (const service of services) {
                const chars = await service.getCharacteristics();
                for (const c of chars) {
                    if (c.properties.write || c.properties.writeWithoutResponse) {
                        writeCharacteristic = c;
                        break;
                    }
                }
                if (writeCharacteristic) break;
            }

            if (!writeCharacteristic) {
                throw new Error('Karakteristik cetak tidak ditemukan.');
            }

            setModalStatus('Mengirim data cetak...');
            const chunkSize = 512;
            for (let i = 0; i < printBytes.length; i += chunkSize) {
                const chunk = printBytes.slice(i, i + chunkSize);
                if (writeCharacteristic.properties.writeWithoutResponse) {
                    await writeCharacteristic.writeValueWithoutResponse(chunk);
                } else {
                    await writeCharacteristic.writeValue(chunk);
                }
            }

            setModalStatus('Cetak Bluetooth Berhasil!');
            setTimeout(() => setModalStatus('Siap mencetak'), 4000);
        } catch (err) {
            console.error(err);
            if (err.name === 'NotFoundError' || err.message.includes('User cancelled')) {
                setModalStatus('Batal memilih printer.');
            } else if (err.message.includes('globally disabled') || err.message.includes('not supported')) {
                setModalStatus('Bluetooth dinonaktifkan di browser ini.');
                alert("Pemberitahuan Browser (Brave / Pengaturan Privasi):\n\nWeb Bluetooth dinonaktifkan oleh browser ini secara bawaan.\n\nCara mengatasi:\n1. Buka POS ini di GOOGLE CHROME atau MICROSOFT EDGE (Rekomendasi Terbaik).\n2. Atau jika tetap menggunakan Brave: buka tab baru 'brave://flags/#enable-web-bluetooth' lalu pilih Enabled & Relaunch.\n3. Atau gunakan tombol 'Cetak Biasa' / 'RawBT'.");
            } else {
                setModalStatus('Gagal: ' + err.message);
                alert('Gagal mencetak: ' + err.message);
            }
        }
    }

    // 2. Cetak Biasa (USB / PDF / Dialog browser) langsung dari modal
    function modalPrintStandard() {
        var printUrl = "<?= $baseURL.'helper/cetak/cetak.php?no='.$id;?>";
        var iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        document.body.appendChild(iframe);

        iframe.onload = function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                window.open(printUrl, '_blank');
            }
            setTimeout(function() {
                if (document.body.contains(iframe)) {
                    document.body.removeChild(iframe);
                }
            }, 5000);
        };
        iframe.src = printUrl;
    }

    // 3. Cetak via RawBT App untuk Android langsung dari modal
    function modalPrintRawBT() {
        const paperSelect = document.getElementById('modalPaperSize');
        const paperWidth = (paperSelect && paperSelect.value === '80') ? 48 : 32;
        const printBytes = buildModalEscPos(paperWidth);

        let binary = '';
        for (let i = 0; i < printBytes.byteLength; i++) {
            binary += String.fromCharCode(printBytes[i]);
        }
        const base64Data = window.btoa(binary);
        window.location.href = 'rawbt:data:application/octet-stream;base64,' + base64Data;
        setModalStatus('Perintah dikirim ke RawBT');
    }
</script>