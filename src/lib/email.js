/**
 * Generic Email Service Utility
 */
export async function sendEmail({ to, subject, html }) {
  try {
    console.log(`[EMAIL SENDING] To: ${to}, Subject: ${subject}`);
    
    // In real app, use nodemailer or resend
    /*
    const nodemailer = require('nodemailer');
    const transporter = nodemailer.createTransport({...});
    await transporter.sendMail({ from: '"ClothStore" <noreply@clothstore.com>', to, subject, html });
    */

    return { success: true };
  } catch (error) {
    console.error('Email Send Error:', error);
    return { success: false, error: error.message };
  }
}
