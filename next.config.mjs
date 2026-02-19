/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      },
    ],
  },
  async rewrites() {
    return [
      {
        source: '/api/products/:path*',
        destination: 'http://localhost:8001/api/products/:path*',
      },
      {
        source: '/api/vendors/:path*',
        destination: 'http://localhost:8001/api/vendors/:path*',
      },
      {
        source: '/api/orders/:path*',
        destination: 'http://localhost:8001/api/orders/:path*',
      },
      {
        source: '/api/sms/:path*',
        destination: 'http://localhost:8001/api/sms/:path*',
      },
      // Proxy users but be careful with auth
      {
        source: '/api/users/:path*',
        destination: 'http://localhost:8001/api/users/:path*',
      },
    ];
  },
};

export default nextConfig;