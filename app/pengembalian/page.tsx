"use client";

import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { toast } from "sonner";
import { Search } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

interface PengembalianRuangan {
  id: string;
  kode_peminjaman: string;
  nama_ruangan: string;
  tanggal_kembali: string;
  kondisi_ruangan: string;
  catatan: string;
}

interface DetailPeminjaman {
  nama_peminjam: string;
  tanggal_pinjam: string;
  tanggal_selesai: string;
  keperluan: string;
}

export default function PengembalianRuangan() {
  const [pengembalianList, setPengembalianList] = useState<PengembalianRuangan[]>([]);
  const [filteredList, setFilteredList] = useState<PengembalianRuangan[]>([]);
  const [kodePeminjaman, setKodePeminjaman] = useState("");
  const [kondisiRuangan, setKondisiRuangan] = useState("");
  const [catatan, setCatatan] = useState("");
  const [searchQuery, setSearchQuery] = useState("");
  const [filterKondisi, setFilterKondisi] = useState("");
  const [detailPeminjaman, setDetailPeminjaman] = useState<DetailPeminjaman | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    try {
      const response = await fetch("/api/pengembalian", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          kode_peminjaman: kodePeminjaman,
          kondisi_ruangan: kondisiRuangan,
          catatan: catatan,
        }),
      });

      const data = await response.json();

      if (response.ok) {
        toast.success("Pengembalian ruangan berhasil dicatat");
        // Reset form
        setKodePeminjaman("");
        setKondisiRuangan("");
        setCatatan("");
        // Refresh data
        fetchPengembalianList();
      } else {
        toast.error(data.error || "Gagal mencatat pengembalian ruangan");
      }
    } catch (error) {
      toast.error("Terjadi kesalahan sistem");
    } finally {
      setIsLoading(false);
    }
  };

  const fetchPengembalianList = async () => {
    try {
      const response = await fetch("/api/pengembalian");
      const data = await response.json();
      setPengembalianList(data);
      setFilteredList(data);
    } catch (error) {
      console.error("Error fetching data:", error);
      toast.error("Gagal mengambil data pengembalian");
    }
  };

  const fetchDetailPeminjaman = async (kodePeminjaman: string) => {
    try {
      const response = await fetch(`/api/peminjaman/${kodePeminjaman}`);
      const data = await response.json();
      setDetailPeminjaman(data);
    } catch (error) {
      console.error("Error fetching detail:", error);
      toast.error("Gagal mengambil detail peminjaman");
    }
  };

  useEffect(() => {
    fetchPengembalianList();
  }, []);

  useEffect(() => {
    const filtered = pengembalianList.filter((item) => {
      const matchQuery = 
        item.kode_peminjaman.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.nama_ruangan.toLowerCase().includes(searchQuery.toLowerCase());
      const matchKondisi = filterKondisi ? item.kondisi_ruangan === filterKondisi : true;
      return matchQuery && matchKondisi;
    });
    setFilteredList(filtered);
  }, [searchQuery, filterKondisi, pengembalianList]);

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Pengembalian Ruangan</h1>
      
      <div className="bg-white rounded-lg shadow p-6 mb-6">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-1">
              Kode Peminjaman
            </label>
            <Input
              type="text"
              value={kodePeminjaman}
              onChange={(e) => setKodePeminjaman(e.target.value)}
              placeholder="Masukkan kode peminjaman"
              required
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium mb-1">
              Kondisi Ruangan
            </label>
            <Select value={kondisiRuangan} onValueChange={setKondisiRuangan}>
              <SelectTrigger>
                <SelectValue placeholder="Pilih kondisi ruangan" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="baik">Baik</SelectItem>
                <SelectItem value="rusak_ringan">Rusak Ringan</SelectItem>
                <SelectItem value="rusak_berat">Rusak Berat</SelectItem>
              </SelectContent>
            </Select>
          </div>
          
          <div>
            <label className="block text-sm font-medium mb-1">
              Catatan
            </label>
            <Input
              type="text"
              value={catatan}
              onChange={(e) => setCatatan(e.target.value)}
              placeholder="Masukkan catatan (opsional)"
            />
          </div>
          
          <Button type="submit" className="w-full" disabled={isLoading}>
            {isLoading ? "Menyimpan..." : "Simpan Pengembalian"}
          </Button>
        </form>
      </div>

      <div className="bg-white rounded-lg shadow">
        <div className="p-6 border-b">
          <h2 className="text-xl font-semibold mb-4">Riwayat Pengembalian</h2>
          
          <div className="flex gap-4 mb-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <Input
                  type="text"
                  placeholder="Cari kode peminjaman atau nama ruangan..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div className="w-48">
              <Select value={filterKondisi} onValueChange={setFilterKondisi}>
                <SelectTrigger>
                  <SelectValue placeholder="Filter kondisi" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="">Semua Kondisi</SelectItem>
                  <SelectItem value="baik">Baik</SelectItem>
                  <SelectItem value="rusak_ringan">Rusak Ringan</SelectItem>
                  <SelectItem value="rusak_berat">Rusak Berat</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        <div className="p-6">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Kode Peminjaman</TableHead>
                <TableHead>Nama Ruangan</TableHead>
                <TableHead>Tanggal Kembali</TableHead>
                <TableHead>Kondisi Ruangan</TableHead>
                <TableHead>Catatan</TableHead>
                <TableHead>Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredList.map((item) => (
                <TableRow key={item.id}>
                  <TableCell>{item.kode_peminjaman}</TableCell>
                  <TableCell>{item.nama_ruangan}</TableCell>
                  <TableCell>{new Date(item.tanggal_kembali).toLocaleString('id-ID')}</TableCell>
                  <TableCell>
                    <span className={`px-2 py-1 rounded-full text-sm ${
                      item.kondisi_ruangan === 'baik' ? 'bg-green-100 text-green-800' :
                      item.kondisi_ruangan === 'rusak_ringan' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-red-100 text-red-800'
                    }`}>
                      {item.kondisi_ruangan.replace('_', ' ')}
                    </span>
                  </TableCell>
                  <TableCell>{item.catatan || '-'}</TableCell>
                  <TableCell>
                    <Dialog>
                      <DialogTrigger asChild>
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => fetchDetailPeminjaman(item.kode_peminjaman)}
                        >
                          Detail
                        </Button>
                      </DialogTrigger>
                      <DialogContent>
                        <DialogHeader>
                          <DialogTitle>Detail Peminjaman</DialogTitle>
                        </DialogHeader>
                        {detailPeminjaman ? (
                          <div className="space-y-4">
                            <div>
                              <h4 className="font-medium">Peminjam</h4>
                              <p>{detailPeminjaman.nama_peminjam}</p>
                            </div>
                            <div>
                              <h4 className="font-medium">Tanggal Pinjam</h4>
                              <p>{new Date(detailPeminjaman.tanggal_pinjam).toLocaleString('id-ID')}</p>
                            </div>
                            <div>
                              <h4 className="font-medium">Tanggal Selesai</h4>
                              <p>{new Date(detailPeminjaman.tanggal_selesai).toLocaleString('id-ID')}</p>
                            </div>
                            <div>
                              <h4 className="font-medium">Keperluan</h4>
                              <p>{detailPeminjaman.keperluan}</p>
                            </div>
                          </div>
                        ) : (
                          <p>Memuat data...</p>
                        )}
                      </DialogContent>
                    </Dialog>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      </div>
    </div>
  );
} 