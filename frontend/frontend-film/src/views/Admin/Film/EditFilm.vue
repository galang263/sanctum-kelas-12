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
      <!-- Field sama persis dengan TambahFilm.vue -->
      <div class="form-group">
        <label>🎬 Judul Film <span class="required">*</span></label>
        <input v-model="form.title" type="text" required />
      </div>

      <div class="form-group">
        <label>🎭 Genre <span class="required">*</span></label>
        <select v-model="form.id_genre" required>
          <option value="">-- Pilih Genre --</option>
          <option v-for="genre in genres" :key="genre.id" :value="genre.id">
            {{ genre.nama_genre }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>🎥 Sutradara <span class="required">*</span></label>
        <input v-model="form.sutradara" type="text" required />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>📅 Tanggal Rilis <span class="required">*</span></label>
          <input v-model="form.tanggal_rilis" type="date" required />
        </div>
        <div class="form-group">
          <label>⏱️ Durasi (menit) <span class="required">*</span></label>
          <input v-model="form.durasi" type="number" min="1" required />
        </div>
      </div>

      <div class="form-group">
        <label>🖼️ URL Poster <span class="required">*</span></label>
        <input v-model="form.poster" type="text" required />
        <img v-if="form.poster" :src="form.poster" alt="Preview" class="poster-preview" />
      </div>

      <div class="form-group">
        <label>🎭 Pilih Aktor <span class="required">*</span></label>
        <div class="checkbox-grid">
          <label v-for="aktor in aktors" :key="aktor.id" class="checkbox-item">
            <input type="checkbox" :value="aktor.id" v-model="form.id_aktor" />
            <span>{{ aktor.nama_aktor }}</span>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>📖 Sinopsis <span class="required">*</span></label>
        <textarea v-model="form.deskripsi" rows="5" required></textarea>
      </div>

      <div class="form-actions">
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

const router = useRouter()
const route  = useRoute()
const filmId = route.params.id   // Ambil :id dari URL

const loadingData   = ref(true)
const loadingSubmit = ref(false)
const successMsg    = ref('')
const errorMsg      = ref('')
const genres        = ref([])
const aktors        = ref([])

const form = reactive({
  title: '', id_genre: '', sutradara: '',
  tanggal_rilis: '', durasi: '', poster: '',
  deskripsi: '', id_aktor: [],
})

onMounted(async () => {
  await ambilDataFilm()
})

const ambilDataFilm = async () => {
  try {
    loadingData.value = true

    // Panggil 3 API sekaligus secara paralel
    const [filmRes, genreRes, aktorRes] = await Promise.all([
      api.get(`/films/${filmId}`),
      api.get('/genre'),
      api.get('/aktor')
    ])

    genres.value = genreRes.data.data
    aktors.value = aktorRes.data.data

    const filmData   = filmRes.data.data

    // Pre-fill form dengan data film yang sudah ada
    form.title         = filmData.title
    form.id_genre      = filmData.id_genre
    form.sutradara     = filmData.sutradara
    form.tanggal_rilis = filmData.tanggal_rilis
    form.durasi        = filmData.durasi
    form.poster        = filmData.poster
    form.deskripsi     = filmData.deskripsi

    // Ekstrak ID aktor: [{id:1, nama:"A"},...] → [1, 2, ...]
    form.id_aktor = filmData.aktor.map(a => a.id)

  } catch (err) {
    errorMsg.value = 'Gagal memuat data film untuk diedit.'
  } finally {
    loadingData.value = false
  }
}

const handleUpdate = async () => {
  if (form.id_aktor.length === 0) {
    errorMsg.value = 'Pilih minimal 1 aktor!'
    return
  }
  try {
    loadingSubmit.value = true
    errorMsg.value      = ''

    // PUT request untuk update data
    await api.put(`/films/${filmId}`, form)

    successMsg.value = 'Data film berhasil diupdate!'
    setTimeout(() => { router.push('/kelola-film') }, 2000)
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    errorMsg.value = 'Gagal mengupdate film!'
  } finally {
    loadingSubmit.value = false
  }
}
</script>