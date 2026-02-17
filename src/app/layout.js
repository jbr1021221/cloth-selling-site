import { Inter } from 'next/font/google';
import './globals.css';
import Navbar from '@/components/layout/Navbar';
import { Toaster } from 'react-hot-toast';

const inter = Inter({ subsets: ['latin'] });

export const metadata = {
  title: 'ClothStore - Best Online Cloth Shop in Bangladesh',
  description: 'Buy quality clothes online at affordable prices',
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <Navbar />
        {children}
        <Toaster position="top-right" />
      </body>
    </html>
  );
}