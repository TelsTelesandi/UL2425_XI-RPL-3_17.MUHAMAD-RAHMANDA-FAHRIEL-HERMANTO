"use client"

import Link from "next/link";

export default function Home() {
  return (
    <div className="container mx-auto p-6">
      <h1 className="text-3xl font-bold mb-8">Sistem Peminjaman Ruangan</h1>
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Link 
          href="/pengembalian"
          className="block p-6 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
        >
          <h2 className="text-xl font-semibold mb-2">Pengembalian Ruangan</h2>
          <p className="text-gray-600">
            Proses pengembalian ruangan dan lihat riwayat pengembalian
          </p>
        </Link>

        <Link 
          href="/peminjaman"
          className="block p-6 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
        >
          <h2 className="text-xl font-semibold mb-2">Peminjaman Ruangan</h2>
          <p className="text-gray-600">
            Ajukan peminjaman ruangan baru
          </p>
        </Link>

        <Link 
          href="/ruangan"
          className="block p-6 bg-white rounded-lg shadow hover:shadow-md transition-shadow"
        >
          <h2 className="text-xl font-semibold mb-2">Daftar Ruangan</h2>
          <p className="text-gray-600">
            Lihat daftar dan ketersediaan ruangan
          </p>
        </Link>
      </div>
    </div>
  );
}