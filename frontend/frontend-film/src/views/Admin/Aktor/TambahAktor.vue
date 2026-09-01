<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/kelola-aktor" class="btn-back">← Kembali</RouterLink>
      <h1>➕ Tambah Aktor Baru</h1>
    </div>

    <!-- Pesan Error -->
    <div v-if="errorMsg" class="alert alert-error">❌ {{ errorMsg }}</div>

    <form @submit.prevent="submitAktor" class="form-card">
      <div class="form-group">
        <label>Nama Aktor <span class="required">*</span></label>
        <input v-model="form.nama_aktor" type="text" placeholder="Contoh: Reza Rahadian" required />
      </div>

      <div class="form-group">
        <label>Gender <span class="required">*</span></label>
        <!-- Perhatikan: value pakai 'L' dan 'P' sesuai database ENUM -->
        <select v-model="form.jenis_kelamin" required>
          <option value="" disabled>Pilih jenis kelamin</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
      </div>

      <div class="form-group">
        <label>Tanggal Lahir <span class="required">*</span></label>
        <input v-model="form.tanggal_lahir" type="date" required />
      </div>

    <div class="form-group">
        <label>umur<span class="required">*</span></label>
        <input v-model="form.umur" type="number" required />
      </div>

      <div class="form-actions">
        <RouterLink to="/kelola-aktor" class="btn-secondary">Batal</RouterLink>
        <button type="submit" :disabled="loading" class="btn btn-primary">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Aktor</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '../../../utils/api'

const router = useRouter()
const form = ref({ nama_aktor: '', jenis_kelamin: '', tanggal_lahir: '' })
const loading = ref(false)
const errorMsg = ref('')

const submitAktor = async () => {
  try {
    loading.value = true
    errorMsg.value = ''
    await api.post('/aktor', form.value)
    router.push('/kelola-aktor')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.page-title { margin-bottom: 24px; }
.page-title h1 { font-size: 28px; color: #1a1a2e; margin-top: 12px; }
.btn-back { color: #666; font-size: 14px; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.alert-error { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }

.form-card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 20px; max-width: 720px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
label { font-size: 13px; font-weight: 600; color: #333; }
.required { color: #e94560; margin-left: 2px; }
input, select { padding: 11px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; }
input:focus, select:focus { border-color: #e94560; box-shadow: 0 0 0 3px rgba(233,69,96,0.1); }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid #f0f0f0; }
.btn-secondary { background: #f0f0f0; color: #555; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; }
.btn-primary { background: #e94560; color: white; padding: 10px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
.btn-primary:hover:not(:disabled) { opacity: 0.9; }
.btn-primary:disabled { background: #999; cursor: not-allowed; }
</style>