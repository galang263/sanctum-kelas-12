<template>
  <div class="container">
    <div class="header">
      <RouterLink to="/kelola-genre" class="btn-back">← Kembali</RouterLink>
      <h1>✏️ Edit Genre</h1>
    </div>

    <p v-if="loadingData" class="loading-text">⏳ Memuat data...</p>

    <div v-else class="form-wrapper">
      <form @submit.prevent="submitGenre">
        <div class="form-group">
          <label>Nama Genre <span class="required">*</span></label>
          <input 
            v-model="form.nama_genre" 
            type="text" 
            required 
            class="form-input" 
            placeholder="Masukkan nama genre..."
          />
        </div>
        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Perubahan</span>
        </button>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted }                  from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api                                 from '../../../utils/api'

const router  = useRouter()
const route   = useRoute()
const genreId = route.params.id

const form        = ref({ nama_genre: '' })
const loadingData = ref(true)
const loading     = ref(false)
const errorMsg    = ref('')

onMounted(async () => {
  try {
    // Karena tidak ada GET /genre/:id, ambil semua lalu filter
    const res     = await api.get('/genre')
    const current = res.data.data.find(g => g.id == genreId)
    if (current) { form.value.nama_genre = current.nama_genre }
    else { router.push('/kelola-genre') }
  } catch (err) { 
    alert('Gagal memuat data') 
  } finally { 
    loadingData.value = false 
  }
})

const submitGenre = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.put(`/genre/${genreId}`, form.value)
    router.push('/kelola-genre')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Container & Header */
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px 0 40px 0;
}

.header {
  margin-bottom: 24px;
}

.btn-back {
  display: inline-block;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  margin-bottom: 12px;
  transition: color 0.2s;
}

.btn-back:hover {
  color: #e94560;
}

h1 {
  font-size: 28px;
  color: #1a1a2e;
  margin: 0;
}

/* Loading & Error Text */
.loading-text {
  color: #64748b;
  font-size: 15px;
  font-weight: 500;
  text-align: center;
  margin-top: 40px;
}

.error-msg {
  color: #dc2626;
  font-size: 14px;
  margin-top: 16px;
  text-align: center;
  font-weight: 500;
  background: #fef2f2;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #fecaca;
}

/* Form Wrapper */
.form-wrapper {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

/* Form Group */
.form-group {
  margin-bottom: 24px;
}

label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.required {
  color: #dc2626;
  margin-left: 2px;
}

/* Input Field */
.form-input {
  width: 100%;
  padding: 12px 16px;
  font-size: 15px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  background: #f8fafc;
  color: #1e293b;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #e94560;
  background: white;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
}

.form-input::placeholder {
  color: #94a3b8;
}

/* Submit Button */
.btn-submit {
  width: 100%;
  padding: 14px;
  background: #e94560;
  color: white;
  font-size: 16px;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

.btn-submit:hover:not(:disabled) {
  background: #d13d56;
}

.btn-submit:active:not(:disabled) {
  transform: translateY(1px);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>