import { NextResponse } from "next/server";
import { sql } from "@vercel/postgres";

export async function GET(
  request: Request,
  { params }: { params: { kode: string } }
) {
  try {
    const kode_peminjaman = params.kode;

    const { rows } = await sql`
      SELECT 
        p.nama_peminjam,
        p.tanggal_pinjam,
        p.tanggal_selesai,
        p.keperluan
      FROM peminjaman p
      WHERE p.kode_peminjaman = ${kode_peminjaman}
    `;

    if (rows.length === 0) {
      return NextResponse.json(
        { error: "Data peminjaman tidak ditemukan" },
        { status: 404 }
      );
    }

    return NextResponse.json(rows[0]);
  } catch (error) {
    console.error("Database Error:", error);
    return NextResponse.json(
      { error: "Terjadi kesalahan saat mengambil detail peminjaman" },
      { status: 500 }
    );
  }
} 