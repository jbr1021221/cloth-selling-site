'use client';

import { useState, useEffect } from 'react';
import { FiX, FiPlus, FiTrash2 } from 'react-icons/fi';

const categories = ['Shirt', 'T-Shirt', 'Pant', 'Jeans', 'Saree', 'Salwar', 'Kurti', 'Others'];
const sizesList = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '38', '40', '42', '44'];

export default function ProductForm({ product, onSubmit, onCancel }) {
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    discountPrice: '',
    category: 'Shirt',
    stock: '',
    sizes: [],
    colors: [],
    images: [],
  });

  const [newImage, setNewImage] = useState('');
  const [newColor, setNewColor] = useState('');

  useEffect(() => {
    if (product) {
      setFormData({
        ...product,
        price: product.price || '',
        discountPrice: product.discountPrice || '',
        stock: product.stock || '',
      });
    }
  }, [product]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSizeToggle = (size) => {
    const updatedSizes = formData.sizes.includes(size)
      ? formData.sizes.filter(s => s !== size)
      : [...formData.sizes, size];
    setFormData({ ...formData, sizes: updatedSizes });
  };

  const addImage = () => {
    if (newImage && !formData.images.includes(newImage)) {
      setFormData({ ...formData, images: [...formData.images, newImage] });
      setNewImage('');
    }
  };

  const removeImage = (index) => {
    const updatedImages = formData.images.filter((_, i) => i !== index);
    setFormData({ ...formData, images: updatedImages });
  };

  const addColor = () => {
    if (newColor && !formData.colors.includes(newColor)) {
      setFormData({ ...formData, colors: [...formData.colors, newColor] });
      setNewColor('');
    }
  };

  const removeColor = (color) => {
    const updatedColors = formData.colors.filter(c => c !== color);
    setFormData({ ...formData, colors: updatedColors });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <div className="bg-white p-6 rounded-lg shadow-lg relative">
      <button 
        onClick={onCancel}
        className="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
      >
        <FiX size={24} />
      </button>
      
      <h2 className="text-2xl font-bold mb-6">{product ? 'Edit Product' : 'Add New Product'}</h2>
      
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700">Product Name</label>
              <input
                type="text"
                name="name"
                required
                value={formData.name}
                onChange={handleChange}
                className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                name="description"
                rows="4"
                required
                value={formData.description}
                onChange={handleChange}
                className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Regular Price</label>
                <input
                  type="number"
                  name="price"
                  required
                  value={formData.price}
                  onChange={handleChange}
                  className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Discount Price</label>
                <input
                  type="number"
                  name="discountPrice"
                  value={formData.discountPrice}
                  onChange={handleChange}
                  className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
              </div>
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Category</label>
                <select
                  name="category"
                  value={formData.category}
                  onChange={handleChange}
                  className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                >
                  {categories.map(cat => (
                    <option key={cat} value={cat}>{cat}</option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Stock Quantity</label>
                <input
                  type="number"
                  name="stock"
                  required
                  value={formData.stock}
                  onChange={handleChange}
                  className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
              </div>
            </div>
          </div>
          
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Available Sizes</label>
              <div className="flex flex-wrap gap-2">
                {sizesList.map(size => (
                  <button
                    key={size}
                    type="button"
                    onClick={() => handleSizeToggle(size)}
                    className={`px-3 py-1 text-xs rounded-full border transition-colors ${
                      formData.sizes.includes(size)
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-white text-gray-600 border-gray-300 hover:border-blue-500'
                    }`}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Colors</label>
              <div className="flex gap-2 mb-2">
                <input
                  type="text"
                  placeholder="Add color (e.g. Red)"
                  value={newColor}
                  onChange={(e) => setNewColor(e.target.value)}
                  className="flex-1 border border-gray-300 rounded-md shadow-sm p-2 text-sm"
                />
                <button
                  type="button"
                  onClick={addColor}
                  className="bg-gray-100 p-2 rounded-md hover:bg-gray-200"
                >
                  <FiPlus />
                </button>
              </div>
              <div className="flex flex-wrap gap-2">
                {formData.colors.map(color => (
                  <span key={color} className="flex items-center gap-1 px-2 py-1 bg-gray-100 text-xs rounded-md">
                    {color}
                    <button type="button" onClick={() => removeColor(color)} className="text-red-500"><FiX size={12} /></button>
                  </span>
                ))}
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Product Images (URLs)</label>
              <div className="flex gap-2 mb-2">
                <input
                  type="text"
                  placeholder="Image URL"
                  value={newImage}
                  onChange={(e) => setNewImage(e.target.value)}
                  className="flex-1 border border-gray-300 rounded-md shadow-sm p-2 text-sm"
                />
                <button
                  type="button"
                  onClick={addImage}
                  className="bg-gray-100 p-2 rounded-md hover:bg-gray-200"
                >
                  <FiPlus />
                </button>
              </div>
              <div className="grid grid-cols-4 gap-2">
                {formData.images.map((img, index) => (
                  <div key={index} className="relative group aspect-square">
                    <img src={img} alt="preview" className="w-full h-full object-cover rounded-md border" />
                    <button 
                      type="button" 
                      onClick={() => removeImage(index)}
                      className="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <FiX size={12} />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
        
        <div className="flex justify-end gap-4 pt-6 border-t font-semibold">
          <button
            type="button"
            onClick={onCancel}
            className="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            className="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            {product ? 'Update Product' : 'Create Product'}
          </button>
        </div>
      </form>
    </div>
  );
}
