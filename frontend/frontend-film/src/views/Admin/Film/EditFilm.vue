<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/kelola-film" class="btn-back">← Batal Edit</RouterLink>
      <h1>✏️ Edit Film</h1>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <div v-if="errorMsg"   class="alert alert-error">❌ {{ errorMsg }}</div>

    <div v-if="loadingData" class="loading-text">⏳ Memuat data film...</div>

    <form v-else @submit.prevent="handleUpdate" class="form-card">
      <div class="form-group">
        <label>🎬 Judul Film <span class="required">*</span></label>
        <input v-model="form.judul_film" type="text" placeholder="Contoh: Avengers Endgame" required />
      </div>

      <div class="form-group">
        <label>🎭 Genre <span class="required">*</span></label>
        <select v-model="form.genre_id" required>
          <option value="">-- Pilih Genre --</option>
          <option v-for="genre in genres" :key="genre.id" :value="genre.id">
            {{ genre.nama_genre }}
          </option>
        </select>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>🎥 Sutradara <span class="required">*</span></label>
          <input v-model="form.sutradara" type="text" placeholder="Nama Sutradara" required />
        </div>
        <div class="form-group">
          <label>⭐ Rating <span class="required">*</span></label>
          <input v-model="form.rating" type="number" placeholder="Contoh: 8.5" min="0" max="10" step="0.1" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>📅 Tahun Rilis <span class="required">*</span></label>
          <input v-model="form.tahun_rilis" type="number" placeholder="2026" min="1900" :max="new Date().getFullYear()" required />
        </div>

        <div class="form-group">
          <label>⏱️ Durasi (menit) <span class="required">*</span></label>
          <input v-model="form.durasi" type="number" placeholder="120" min="1" required />
        </div>
      </div>

      <div class="form-group">
        <label>🖼️ URL Poster <span class="required">*</span></label>
        <input v-model="form.poster" type="text" placeholder="https://..." required />
        <img v-if="form.poster" :src="form.poster" alt="Preview Poster" class="poster-preview" />
      </div>

      <div class="form-group">
        <label>🎭 Pilih Aktor <span class="required">*</span></label>
        <div class="checkbox-grid">
          <label v-for="aktor in aktors" :key="aktor.id" class="checkbox-item">
            <input type="checkbox" :value="aktor.id" v-model="form.aktor_id" />
            <span>{{ aktor.nama_aktor }}</span>
          </label>
        </div>
        <p class="hint">Pilih minimal 1 aktor</p>
      </div>

      <div class="form-group">
        <label>📖 Sinopsis <span class="required">*</span></label>
        <textarea v-model="form.deskripsi" rows="5" placeholder="Tulis sinopsis film..." required></textarea>
      </div>

      <div class="form-actions">
        <RouterLink to="/kelola-film" class="btn-secondary">Batal</RouterLink>
        <button type="submit" :disabled="loadingSubmit" class="btn btn-primary">
          <span v-if="loadingSubmit">⏳ Mengupdate...</span>
          <span v-else>💾 Update Film</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted }           from 'vue'
import { RouterLink, useRouter, useRoute }    from 'vue-router'
import api                                    from '../../../utils/api'

const router        = useRouter()
const route         = useRoute()
const filmId        = route.params.id

const loadingData   = ref(true)
const loadingSubmit = ref(false)
const successMsg    = ref('')
const errorMsg      = ref('')
const genres        = ref([])
const aktors        = ref([])

// Menggunakan tahun_rilis dan struktur persis seperti form tambah film
const form = reactive({
  judul_film:  '',
  genre_id:    '',
  sutradara:   '',
  rating:      '',
  tahun_rilis: '',
  durasi:      '',
  poster:      '',
  deskripsi:   '',
  aktor_id:    [],
})

onMounted(async () => {
  await ambilDataFilm()
})

const ambilDataFilm = async () => {
  try {
    loadingData.value = true
    errorMsg.value = ''

    try {
      const genreRes = await api.get('/genre')
      genres.value = genreRes.data.data
    } catch (err) {
      console.error('Error memuat Genre:', err)
    }

    try {
      const aktorRes = await api.get('/aktor')
      aktors.value = aktorRes.data.data
    } catch (err) {
      console.error('Error memuat Aktor:', err)
    }

    const filmRes  = await api.get(`/film/${filmId}`)
    const filmData = filmRes.data.data

    // Mapping data ke form dengan fallback jika backend menggunakan nama properti lama
    form.judul_film  = filmData.judul_film || filmData.title || ''
    form.genre_id    = filmData.genre_id || filmData.id_genre || ''
    form.sutradara   = filmData.sutradara || ''
    form.rating      = filmData.rating || ''
    form.tahun_rilis = filmData.tahun_rilis || filmData.tanggal_rilis || ''
    form.durasi      = filmData.durasi || ''
    form.poster      = filmData.poster || ''
    form.deskripsi   = filmData.deskripsi || ''

    if (filmData.aktor && Array.isArray(filmData.aktor)) {
      form.aktor_id = filmData.aktor.map(a => a.id)
    } else if (filmData.aktors && Array.isArray(filmData.aktors)) {
      form.aktor_id = filmData.aktors.map(a => a.id)
    }

  } catch (err) {
    console.error('Detail Error API:', err.response || err)
    errorMsg.value = 'Gagal memuat data film.'
  } finally {
    loadingData.value = false
  }
}

const handleUpdate = async () => {
  if (form.aktor_id.length === 0) {
    errorMsg.value = 'Pilih minimal 1 aktor!'
    return
  }
  
  try {
    loadingSubmit.value = true
    errorMsg.value      = ''
    successMsg.value    = ''

    await api.put(`/film/${filmId}`, form)

    successMsg.value = 'Data film berhasil diupdate!'
    setTimeout(() => { router.push('/kelola-film') }, 2000)
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    console.error('Detail Error Update:', err.response || err)
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      errorMsg.value = Object.values(errors)[0][0]
    } else {
      errorMsg.value = err.response?.data?.message || 'Gagal mengupdate film! Cek kembali form atau koneksi backend.'
    }
  } finally {
    loadingSubmit.value = false
  }
}
</script>

<style scoped>
.container { max-width: 800px; margin: 0 auto; padding: 20px 0 40px 0; }
.page-title { margin-bottom: 24px; }
.page-title h1 { font-size: 28px; color: #1a1a2e; margin-top: 12px; }
.btn-back { color: #666; font-size: 14px; text-decoration: none; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.alert { padding: 14px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 15px; font-weight: 500; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.loading-text { text-align: center; color: #64748b; font-size: 16px; font-weight: 500; margin-top: 40px; }

.form-card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

label { font-size: 13px; font-weight: 600; color: #333; }
.required { color: #e94560; margin-left: 2px; }
input, select, textarea { padding: 11px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; background: #f8fafc; color: #1e293b; width: 100%; box-sizing: border-box; }
input:focus, select:focus, textarea:focus { border-color: #e94560; background: #fff; box-shadow: 0 0 0 3px rgba(233,69,96,0.1); }
textarea { resize: vertical; }

.poster-preview { margin-top: 10px; width: 120px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; display: block; }
.checkbox-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; max-height: 220px; overflow-y: auto; background: #f4f4f8; padding: 12px; border-radius: 10px; border: 2px solid #e0e0e0; }
.checkbox-item { display: flex; align-items: center; gap: 6px; background: #fff; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: normal; transition: background 0.2s; border: 1px solid #e0e0e0; margin: 0; }
.checkbox-item:has(input:checked) { background: #fee2e2; color: #e94560; font-weight: 600; border-color: #fecaca; }
.checkbox-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: #e94560; }
.hint { font-size: 12px; color: #999; margin-top: 4px; }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #f0f0f0; margin-top: 12px; }
.btn-secondary { background: #f0f0f0; color: #555; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; }
.btn-secondary:hover { background: #e2e2e2; }
.btn { padding: 12px 32px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
.btn-primary { background: #e94560; color: white; }
.btn-primary:hover:not(:disabled) { background: #d13d56; }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
</style>