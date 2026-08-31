<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/kelola-genre" class="btn-back">← Kembali</RouterLink>
      <h1>➕ Tambah Genre Baru</h1>
    </div>

    <div class="form-card">
      <form @submit.prevent="submitGenre">
        <div class="form-group">
          <label>Nama Genre <span class="required">*</span></label>
          <input 
            v-model="form.nama_genre" 
            type="text" 
            placeholder="Contoh: Action" 
            required 
            class="form-input" 
          />
        </div>

        <p v-if="errorMsg" class="error-msg">⚠️ {{ errorMsg }}</p>

        <div class="form-actions">
          <RouterLink to="/kelola-genre" class="btn-secondary">Batal</RouterLink>
          <button type="submit" :disabled="loading" class="btn-submit">
            <span v-if="loading">⏳ Menyimpan...</span>
            <span v-else>💾 Simpan Genre</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref }                 from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api                    from '../../../utils/api'

const router   = useRouter()
const form     = ref({ nama_genre: '' })
const loading  = ref(false)
const errorMsg = ref('')

const submitGenre = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.post('/genre', form.value)
    router.push('/kelola-genre')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 24px 16px;
}

.page-title {
  margin-bottom: 24px;
}

.page-title h1 {
  font-size: 28px;
  color: #1a1a2e;
  margin-top: 12px;
}

.btn-back {
  color: #666;
  font-size: 14px;
  text-decoration: none;
  transition: color 0.2s;
}

.btn-back:hover {
  color: #e94560;
  text-decoration: none;
}

.form-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  max-width: 720px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}

.required {
  color: #e94560;
  margin-left: 2px;
}

input {
  padding: 11px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

input:focus {
  border-color: #e94560;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
}

.error-msg {
  color: #e94560;
  font-size: 13px;
  font-weight: 500;
  margin: 0;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.btn-secondary {
  background: #f0f0f0;
  color: #555;
  padding: 10px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.btn-submit {
  background: #e94560;
  color: white;
  padding: 10px 24px;
  border-radius: 10px;
  border: none;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s, box-shadow 0.2s;
}

.btn-submit:hover:not(:disabled) {
  background: #d63852;
  box-shadow: 0 4px 12px rgba(233, 69, 96, 0.3);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>