<script setup>
import { computed } from 'vue'
import { paletteFor, MUTED_PALETTE } from '../scooterPalette'

const props = defineProps({
  variant: { type: [Number, String], default: 0 }, // sert à choisir la couleur
  muted: { type: Boolean, default: false }, // version grise (pages vides)
})

const palette = computed(() => (props.muted ? MUTED_PALETTE : paletteFor(props.variant)))
const vars = computed(() => ({ '--body': palette.value.body, '--dark': palette.value.dark }))
const label = computed(() => `Scooter ${palette.value.name}`)
</script>

<template>
  <svg class="scooter-art" viewBox="0 0 400 240" role="img" :aria-label="label" :style="vars">
    <!-- trait de route : il déborde du dessin, c'est le conteneur qui le coupe -->
    <path
      d="M-800 220H1200"
      fill="none"
      stroke-width="3"
      stroke-dasharray="18 16"
      style="stroke: var(--road, rgba(11, 42, 60, 0.28))"
    />

    <!-- ombre au sol -->
    <ellipse cx="205" cy="214" rx="158" ry="8" fill="#0b2a3c" opacity=".14" />

    <!-- roue arrière -->
    <circle cx="98" cy="176" r="33" fill="#13232d" />
    <circle cx="98" cy="176" r="19" fill="#dfe6ea" />
    <circle cx="98" cy="176" r="19" fill="none" stroke="#9fb0ba" stroke-width="2" />
    <circle cx="98" cy="176" r="6" fill="#5a6c78" />

    <!-- roue avant -->
    <circle cx="318" cy="176" r="33" fill="#13232d" />
    <circle cx="318" cy="176" r="19" fill="#dfe6ea" />
    <circle cx="318" cy="176" r="19" fill="none" stroke="#9fb0ba" stroke-width="2" />
    <circle cx="318" cy="176" r="6" fill="#5a6c78" />

    <!-- fourche et colonne de direction -->
    <path d="M296 62 L321 176" stroke="#13232d" stroke-width="9" stroke-linecap="round" />
    <path d="M296 62 L321 176" stroke="#5a6c78" stroke-width="2.5" stroke-linecap="round" />

    <!-- garde-boue avant -->
    <path d="M287 151 C298 122 344 122 354 153 L345 156 C336 138 305 138 297 157 Z" fill="var(--body)" />

    <!-- plancher -->
    <path d="M205 163 L274 163 C286 163 287 178 274 179 L205 179 Z" fill="var(--dark)" />

    <!-- tablier avant -->
    <path d="M254 171 C249 132 258 96 281 60 L306 67 C295 100 297 136 308 169 Z" fill="var(--body)" />
    <path d="M268 150 C266 124 272 98 288 76" stroke="#fff" stroke-opacity=".38" stroke-width="4" stroke-linecap="round" fill="none" />

    <!-- carrosserie arrière en arche autour de la roue -->
    <path d="M52 172 C41 126 76 93 126 91 L198 91 C229 91 241 116 235 148 L229 172 L142 172 C138 132 60 132 56 172 Z" fill="var(--body)" />
    <path d="M86 108 C122 98 180 98 216 108" stroke="#fff" stroke-opacity=".38" stroke-width="4" stroke-linecap="round" fill="none" />

    <!-- selle -->
    <path d="M110 92 C110 77 126 70 150 70 L208 70 C225 70 229 83 220 92 Z" fill="#1b2b34" />
    <path d="M122 78 C140 74 190 74 208 78" stroke="#fff" stroke-opacity=".14" stroke-width="3" stroke-linecap="round" fill="none" />

    <!-- feu arrière -->
    <rect x="44" y="118" width="9" height="17" rx="3.5" fill="#ff4b3e" />

    <!-- guidon, poignée, rétroviseur -->
    <path d="M276 55 L324 46" stroke="#13232d" stroke-width="7" stroke-linecap="round" />
    <circle cx="326" cy="46" r="5.5" fill="#13232d" />
    <path d="M292 52 L284 27" stroke="#13232d" stroke-width="3" stroke-linecap="round" />
    <ellipse cx="283" cy="22" rx="10" ry="6.5" fill="#13232d" transform="rotate(-18 283 22)" />

    <!-- phare -->
    <path d="M301 63 C318 58 333 67 331 82 C314 87 301 80 301 63 Z" fill="#fff3c4" stroke="#13232d" stroke-width="3" stroke-linejoin="round" />
  </svg>
</template>

<style scoped>
.scooter-art {
  display: block;
  width: 100%;
  height: auto;
  overflow: visible;
}
</style>
