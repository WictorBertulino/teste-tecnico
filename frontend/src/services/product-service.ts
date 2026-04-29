import type { Product, ProductFormData, ProductListResponse } from '@/types/product'
import { api } from './api'

interface ProductFilters {
  search?: string
  ativo?: '' | 'true' | 'false'
  page?: number
  per_page?: number
}

export async function fetchProducts(filters: ProductFilters) {
  const response = await api.get<ProductListResponse>('/v1/products', {
    params: filters,
  })

  return response.data
}

export async function createProduct(payload: ProductFormData) {
  const response = await api.post<{ data: Product }>('/v1/products', payload)

  return response.data.data
}

export async function updateProduct(productId: number, payload: ProductFormData) {
  const response = await api.put<{ data: Product }>(`/v1/products/${productId}`, payload)

  return response.data.data
}

export async function deleteProduct(productId: number) {
  await api.delete(`/v1/products/${productId}`)
}
