import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import { Toaster } from 'sonner'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Sistem Peminjaman Ruangan',
  description: 'Aplikasi manajemen peminjaman ruangan',
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="id">
      <body className={inter.className}>
        <main className="min-h-screen bg-gray-100">
          {children}
        </main>
        <Toaster position="top-center" richColors />
      </body>
    </html>
  )
}
