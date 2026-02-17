<?php
/*
* PROSES TAMPIL
*/
class view
{
    protected $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function member()
    {
        $sql = "select member.*, login.*
                from member inner join login on member.id_member = login.id_member";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function member_edit($id)
    {
        $sql = "select member.*, login.*
                from member inner join login on member.id_member = login.id_member
                where member.id_member= ?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function toko()
    {
        $sql = "select*from toko where id_toko='1'";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function kategori()
    {
        $sql = "select*from kategori";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang()
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori 
                ORDER BY id DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_stok()
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori 
                where stok <= 3 
                ORDER BY id DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_edit($id)
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori
                where id_barang=?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function barang_cari($cari)
    {
        $param = "%{$cari}%";
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori
                where id_barang like ? or nama_barang like ? or merk like ?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($param, $param, $param));
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_id()
    {
        $sql = 'SELECT * FROM barang ORDER BY id DESC';
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();

        $urut = substr($hasil['id_barang'], 2, 3);
        $tambah = (int) $urut + 1;
        if (strlen($tambah) == 1) {
            $format = 'BR00'.$tambah.'';
        } elseif (strlen($tambah) == 2) {
            $format = 'BR0'.$tambah.'';
        } else {
            $ex = explode('BR', $hasil['id_barang']);
            $no = (int) $ex[1] + 1;
            $format = 'BR'.$no.'';
        }
        return $format;
    }

    public function kategori_edit($id)
    {
        $sql = "select*from kategori where id_kategori=?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function kategori_row()
    {
        $sql = "select*from kategori";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> rowCount();
        return $hasil;
    }

    public function barang_row()
    {
        $sql = "select*from barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> rowCount();
        return $hasil;
    }

    public function barang_stok_row()
    {
        $sql ="SELECT SUM(stok) as jml FROM barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function barang_beli_row()
    {
        $sql ="SELECT SUM(harga_beli) as beli FROM barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jual_row()
    {
        $sql ="SELECT SUM(jumlah) as stok FROM nota";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jual()
    {
        $sql ="SELECT MIN(nota.id_nota) as id_nota_min, MAX(nota.id_nota) as id_nota_max,
                nota.tanggal_input, nota.periode, nota.id_member, nota.id_customer,
                COALESCE(MAX(nota.diskon_persen), 0) as diskon_persen, 
                COALESCE(MAX(nota.diskon_nominal), 0) as diskon_nominal,
                COALESCE(MAX(nota.bayar), 0) as bayar,
                COALESCE(MAX(nota.kembalian), 0) as kembalian,
                GROUP_CONCAT(CONCAT(barang.nama_barang, ' (', nota.jumlah, ' ', barang.satuan_barang, ')') SEPARATOR ', ') as barang_list,
                GROUP_CONCAT(barang.id_barang SEPARATOR ', ') as id_barang_list,
                SUM(nota.jumlah) as jumlah_total,
                SUM(barang.harga_beli * nota.jumlah) as modal_total,
                SUM(nota.total) as total,
                member.nm_member,
                customer.nama_customer
                FROM nota 
                LEFT JOIN barang ON barang.id_barang=nota.id_barang 
                LEFT JOIN member ON member.id_member=nota.id_member 
                LEFT JOIN customer ON customer.id_customer=nota.id_customer
                WHERE nota.periode = ?
                GROUP BY nota.tanggal_input, nota.id_member, nota.id_customer
                ORDER BY MIN(nota.id_nota) DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array(date('m-Y')));
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function periode_jual($periode)
    {
        $sql ="SELECT MIN(nota.id_nota) as id_nota_min, MAX(nota.id_nota) as id_nota_max,
                nota.tanggal_input, nota.periode, nota.id_member, nota.id_customer,
                COALESCE(MAX(nota.diskon_persen), 0) as diskon_persen, 
                COALESCE(MAX(nota.diskon_nominal), 0) as diskon_nominal,
                COALESCE(MAX(nota.bayar), 0) as bayar,
                COALESCE(MAX(nota.kembalian), 0) as kembalian,
                GROUP_CONCAT(CONCAT(barang.nama_barang, ' (', nota.jumlah, ' ', barang.satuan_barang, ')') SEPARATOR ', ') as barang_list,
                GROUP_CONCAT(barang.id_barang SEPARATOR ', ') as id_barang_list,
                SUM(nota.jumlah) as jumlah_total,
                SUM(barang.harga_beli * nota.jumlah) as modal_total,
                SUM(nota.total) as total,
                member.nm_member,
                customer.nama_customer
                FROM nota 
                LEFT JOIN barang ON barang.id_barang=nota.id_barang 
                LEFT JOIN member ON member.id_member=nota.id_member 
                LEFT JOIN customer ON customer.id_customer=nota.id_customer
                WHERE nota.periode = ?
                GROUP BY nota.tanggal_input, nota.id_member, nota.id_customer
                ORDER BY MIN(nota.id_nota) ASC";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($periode));
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function hari_jual($hari)
    {
        $ex = explode('-', $hari);
        $monthNum  = $ex[1];
        $monthName = date('F', mktime(0, 0, 0, $monthNum, 10));
        if ($ex[2] > 9) {
            $tgl = $ex[2];
        } else {
            $tgl1 = explode('0', $ex[2]);
            $tgl = $tgl1[1];
        }
        $cek = $tgl.' '.$monthName.' '.$ex[0];
        $param = "%{$cek}%";
        $sql ="SELECT MIN(nota.id_nota) as id_nota_min, MAX(nota.id_nota) as id_nota_max,
                nota.tanggal_input, nota.periode, nota.id_member, nota.id_customer,
                COALESCE(MAX(nota.diskon_persen), 0) as diskon_persen, 
                COALESCE(MAX(nota.diskon_nominal), 0) as diskon_nominal,
                COALESCE(MAX(nota.bayar), 0) as bayar,
                COALESCE(MAX(nota.kembalian), 0) as kembalian,
                GROUP_CONCAT(CONCAT(barang.nama_barang, ' (', nota.jumlah, ' ', barang.satuan_barang, ')') SEPARATOR ', ') as barang_list,
                GROUP_CONCAT(barang.id_barang SEPARATOR ', ') as id_barang_list,
                SUM(nota.jumlah) as jumlah_total,
                SUM(barang.harga_beli * nota.jumlah) as modal_total,
                SUM(nota.total) as total,
                member.nm_member,
                customer.nama_customer
                FROM nota 
                LEFT JOIN barang ON barang.id_barang=nota.id_barang 
                LEFT JOIN member ON member.id_member=nota.id_member 
                LEFT JOIN customer ON customer.id_customer=nota.id_customer
                WHERE nota.tanggal_input LIKE ?
                GROUP BY nota.tanggal_input, nota.id_member, nota.id_customer
                ORDER BY MIN(nota.id_nota) ASC";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($param));
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function penjualan()
    {
        $sql ="SELECT penjualan.* , barang.id_barang, barang.nama_barang, member.id_member,
                member.nm_member from penjualan 
                left join barang on barang.id_barang=penjualan.id_barang 
                left join member on member.id_member=penjualan.id_member
                ORDER BY id_penjualan";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function jumlah()
    {
        $sql ="SELECT SUM(total) as bayar FROM penjualan";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jumlah_nota()
    {
        $sql ="SELECT SUM(total) as bayar FROM nota";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jml()
    {
        $sql ="SELECT SUM(harga_beli*stok) as byr FROM barang";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    // Fungsi untuk Customer
    public function customer()
    {
        $sql = "SELECT * FROM customer ORDER BY id_customer DESC";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return [];
        }
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function customer_edit($id)
    {
        $sql = "SELECT * FROM customer WHERE id_customer = ?";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return false;
        }
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function customer_by_phone($phone)
    {
        $sql = "SELECT * FROM customer WHERE no_telepon = ?";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return false;
        }
        $row -> execute(array($phone));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function customer_aktif()
    {
        $sql = "SELECT * FROM customer WHERE status = 'aktif' ORDER BY nama_customer ASC";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return [];
        }
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    // Fungsi untuk Setting Diskon
    public function setting_diskon()
    {
        $sql = "SELECT * FROM setting_diskon WHERE status = 'aktif' ORDER BY min_belanja ASC";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return [];
        }
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function get_diskon_by_total($total)
    {
        $sql = "SELECT * FROM setting_diskon WHERE status = 'aktif' AND min_belanja <= ? ORDER BY diskon_persen DESC LIMIT 1";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return false;
        }
        $row -> execute(array($total));
        $hasil = $row -> fetch();
        return $hasil;
    }

    // Fungsi untuk Reminder Log
    public function reminder_log()
    {
        $sql = "SELECT reminder_log.*, customer.nama_customer, barang.nama_barang 
                FROM reminder_log 
                LEFT JOIN customer ON reminder_log.id_customer = customer.id_customer
                LEFT JOIN barang ON reminder_log.id_barang = barang.id_barang
                ORDER BY id_log DESC";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return [];
        }
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    // Fungsi untuk Customer Barang (tracking pembelian)
    public function customer_barang_tracking($id_customer, $id_barang)
    {
        $sql = "SELECT * FROM customer_barang WHERE id_customer = ? AND id_barang = ?";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return false;
        }
        $row -> execute(array($id_customer, $id_barang));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function customer_perlu_reminder()
    {
        // Ambil customer yang sudah 3 hari sejak terakhir beli dan barang nya stok habis/hampir habis
        $sql = "SELECT DISTINCT cb.*, c.nama_customer, c.no_telepon, b.nama_barang, b.stok
                FROM customer_barang cb
                LEFT JOIN customer c ON cb.id_customer = c.id_customer
                LEFT JOIN barang b ON cb.id_barang = b.id_barang
                WHERE c.status = 'aktif' 
                AND b.stok <= 3
                AND cb.terakhir_beli <= DATE_SUB(NOW(), INTERVAL 3 DAY)
                ORDER BY cb.terakhir_beli ASC";
        $row = $this-> db -> prepare($sql);
        if (!$row) {
            return [];
        }
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }
}
