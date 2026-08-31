<template>
  <div class="container">
    <RouterLink to="/kelola-aktor" class="btn-back">← Kembali</RouterLink>
    <h1 style="margin: 12px 0 24px">✏️ Edit Aktor</h1>

    <p v-if="loadingData" class="loading-text">⏳ Memuat data...</p>

    <div v-else class="form-wrapper">
      <form @submit.prevent="submitAktor">
        <div class="form-group">
          <label>Nama Aktor <span class="required">*</span></label>
          <input v-model="form.nama_aktor" type="text" required class="form-input" />
        </div>

        <div class="form-group">
          <label>Gender <span class="required">*</span></label>
          <select v-model="form.gender" required class="form-input">
            <option value="" disabled>Pilih Gender</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>

        <div class="form-group">
          <label>Tanggal Lahir <span class="required">*</span></label>
          <input v-model="form.tanggal_lahir" type="date" required class="form-input" />
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
import { ref, onMounted }            from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api                           from '../../../utils/api'

const router  = useRouter()
const route   = useRoute()
const aktorId = route.params.id

const form        = ref({ nama_aktor: '', gender: '', tanggal_lahir: '' })
const loadingData = ref(true)
const loading     = ref(false)
const errorMsg    = ref('')

onMounted(async () => {
  try {
    const res     = await api.get('/aktor')
    const current = res.data.data.find(a => a.id == aktorId)
    if (current) {
      form.value.nama_aktor    = current.nama_aktor
      form.value.gender        = current.gender
      form.value.tanggal_lahir = current.tanggal_lahir
    } else { router.push('/kelola-aktor') }
  } catch (err) { alert('Gagal memuat data') }
  finally { loadingData.value = false }
})

const submitAktor = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.put(`/aktor/${aktorId}`, form.value)
    router.push('/kelola-aktor')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>