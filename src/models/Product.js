import mongoose from 'mongoose';

const ProductSchema = new mongoose.Schema({
  name: {
    type: String,
    required: [true, 'Please provide product name'],
    trim: true,
  },
  description: {
    type: String,
    required: [true, 'Please provide product description'],
  },
  category: {
    type: String,
    required: [true, 'Please provide category'],
    enum: ['Shirt', 'T-Shirt', 'Pant', 'Jeans', 'Saree', 'Salwar', 'Kurti', 'Others'],
  },
  subcategory: {
    type: String,
  },
  price: {
    type: Number,
    required: [true, 'Please provide price'],
  },
  discountPrice: {
    type: Number,
  },
  images: [{
    type: String,
  }],
  sizes: [{
    type: String,
    enum: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36', '38', '40'],
  }],
  colors: [{
    type: String,
  }],
  stock: {
    type: Number,
    required: true,
    default: 0,
  },
  sku: {
    type: String,
    unique: true,
  },
  vendor: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Vendor',
  },
  isActive: {
    type: Boolean,
    default: true,
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
  updatedAt: {
    type: Date,
    default: Date.now,
  },
});

// Update the updatedAt field on save
ProductSchema.pre('save', function(next) {
  this.updatedAt = Date.now();
  next();
});

export default mongoose.models.Product || mongoose.model('Product', ProductSchema);