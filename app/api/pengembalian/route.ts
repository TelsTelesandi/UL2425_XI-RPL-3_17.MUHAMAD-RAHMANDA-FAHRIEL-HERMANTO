import { NextResponse } from "next/server";
import { sql } from "@vercel/postgres";
import { auth } from "@/lib/auth";

export async function GET() {
  try {
    const { rows } = await sql`
      SELECT 
        p.id,
        p.kode_peminjaman,
        r.nama as nama_ruangan,
        p.tanggal_kembali,
        p.kondisi_ruangan,
        p.catatan
      FROM pengembalian p
      JOIN peminjaman pm ON p.kode_peminjaman = pm.kode_peminjaman
      JOIN ruangan r ON pm.id_ruangan = r.id
      ORDER BY p.tanggal_kembali DESC
    `;
    
    return NextResponse.json(rows);
  } catch (error) {
    console.error("Database Error:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat mengambil data pengembalian" },
      { status: 500 }
    );
  }
}

export async function POST(request: Request) {
  try {
    const { kode_peminjaman, kondisi_ruangan, catatan } = await request.json();

    // Validasi input
    if (!kode_peminjaman || !kondisi_ruangan) {
      return NextResponse.json(
        { error: "Kode peminjaman dan kondisi ruangan harus diisi" },
        { status: 400 }
      );
    }

    // Cek apakah peminjaman ada dan belum dikembalikan
    const { rows: peminjaman } = await sql`
      SELECT id FROM peminjaman 
      WHERE kode_peminjaman = ${kode_peminjaman}
      AND status = 'dipinjam'
    `;

    if (peminjaman.length === 0) {
      return NextResponse.json(
        { error: "Peminjaman tidak ditemukan atau sudah dikembalikan" },
        { status: 400 }
      );
    }

    // Catat pengembalian
    const { rows } = await sql`
      INSERT INTO pengembalian (
        kode_peminjaman,
        tanggal_kembali,
        kondisi_ruangan,
        catatan
      ) VALUES (
        ${kode_peminjaman},
        CURRENT_TIMESTAMP,
        ${kondisi_ruangan},
        ${catatan}
      )
      RETURNING id
    `;

    // Update status peminjaman
    await sql`
      UPDATE peminjaman
      SET status = 'selesai'
      WHERE kode_peminjaman = ${kode_peminjaman}
    `;

    return NextResponse.json({
      message: "Pengembalian berhasil dicatat",
      id: rows[0].id
    });
  } catch (error) {
    console.error("Database Error:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat mencatat pengembalian" },
      { status: 500 }
    );
  }
} 