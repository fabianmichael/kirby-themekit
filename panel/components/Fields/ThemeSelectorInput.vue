<template>
  <div class="theme-selector-input">
    <ul>
      <li
        v-if="none"
        class="theme-selector-input__option"
      >
        <input
          :id="id + '-none'"
          :value="''"
          :name="id"
          :checked="!value"
          type="radio"
          @change="onInput('')"
        />
        <label :for="id + '-none'">
          <span class="theme-selector-input__preview">
            <svg style="opacity: .2;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><g stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" fill="none" stroke="currentColor" stroke-miterlimit="10"><line x1="39.6" y1="8.4" x2="8.5" y2="39.5" stroke-linecap="butt"></line> <circle cx="24" cy="24" r="22"></circle></g></svg>
          </span>
          <span class="theme-selector-input__theme-details">
            <span class="theme-selector-input__indicator"></span>
            <span class="theme-selector-input__theme-name">Standard</span>
          </span>
        </label>
      </li>
      <li
        v-for="(theme, index) in themes"
        :key="index"
        class="theme-selector-input__option"
        :style="`--bg: ${theme.background}; --foreground: ${theme.foreground}`"
      >
        <input
          :id="id + '-' + theme.slug"
          :value="theme.slug"
          :name="id"
          :checked="value === theme.slug"
          type="radio"
          @change="onInput(theme.slug)"
        />
        <label :for="id + '-' + theme.slug">
          <span class="theme-selector-input__preview">Aa</span>
          <span class="theme-selector-input__complementary">
            <span
              v-for="(color, index) in previewColors"
              :key="index"
              class="theme-selector-input__swatch"
              :style="`--swatch: ${theme[color]}`"
            />
          </span>
          <span class="theme-selector-input__theme-details">
            <span class="theme-selector-input__indicator"></span>
            <span class="theme-selector-input__theme-name">{{ theme.title }}</span>
          </span>
        </label>
      </li>
      <li
        v-if="custom"
        class="theme-selector-input__option"
      >
        <input
          :id="id + '-custom'"
          value="custom"
          :name="id"
          :checked="value === 'custom'"
          type="radio"
          @change="onInput('custom')"
        />
        <label :for="id + '-custom'">
          <span class="theme-selector-input__preview">
            <svg xmlns="http://www.w3.org/2000/svg" width="540" height="540" viewBox="0 0 540 540">
              <path d="M511.5 205.3a239 239 0 0 1 0 129.4l-24.2 14.6L270 270l220.2-77.9z" fill="#fefe33"/>
              <path d="M334.7 28.5c44.4 12 79.5 32.2 112 64.7l.7 24.9L270 270l43-229.5z" fill="#fb9902"/>
              <path d="M446.8 93.2a238.9 238.9 0 0 1 64.7 112.1L270 270z" fill="#fabc02"/>
              <path d="M93.2 93.2a238.9 238.9 0 0 1 112.1-64.7l26 16L270 270 92.7 120z" fill="#fe2712"/>
              <path d="M205.3 28.5a239 239 0 0 1 129.4 0L270 270z" fill="#fd5308"/>
              <path d="M28.5 334.7a239 239 0 0 1 0-129.4l27.8-18.7L270 270 55.9 349.4z" fill="#8601af"/>
              <path d="M28.5 205.3c12-44.4 32.2-79.5 64.7-112L270 270z" fill="#a7194b"/>
              <path d="M205.3 511.5a238.9 238.9 0 0 1-112-64.7l2-28.2L270 270l-39 229.7z" fill="#0247fe"/>
              <path d="M93.2 446.8a238.9 238.9 0 0 1-64.7-112.1L270 270z" fill="#3d01a4"/>
              <path d="M446.8 446.8a238.9 238.9 0 0 1-112.1 64.7l-24.2-15.1L270 270l176 151.2z" fill="#66b032"/>
              <path d="M334.7 511.5a239 239 0 0 1-129.4 0L270 270z" fill="#0391ce"/>
              <path d="M511.5 334.7a238.9 238.9 0 0 1-64.7 112L270 270l241.5 64.7z" fill="#d0ea2b"/>
              <circle cx="270" cy="270" r="153.8" fill="#fff"/>
            </svg>
          </span>
          <span class="theme-selector-input__theme-details">
            <span class="theme-selector-input__indicator"></span>
            <span class="theme-selector-input__theme-name">Custom</span>
          </span>
        </label>
      </li>
    </ul>
  </div>
</template>

<script>

export default {
  inheritAttrs: false,
  props: {
    autofocus: Boolean,
    disabled: Boolean,
    id: {
      type: [Number, String],
      default() {
        return this._uid;
      }
    },
    previewColors: {
      type: [Array],
      default() {
        return [];
      }
    },
    required: Boolean,
    themes: Object,
    custom: Boolean,
    none: Boolean,
    value: String,
  },
  watch: {
    value() {
      this.onInvalid()
    }
  },
  mounted() {
    this.onInvalid()

    if (this.$props.autofocus) {
      this.focus()
    }
  },
  methods: {
    focus() {
      (this.$el.querySelector("input[checked]") || this.$el.querySelector("input")).focus()
    },
    onInput(value) {
      this.$emit("input", value)
    },
    onInvalid() {
      this.$emit("invalid", this.$v.$invalid, this.$v)
    },
    select() {
      this.focus()
    },
    onReset() {
      this.$emit("reset")
    }
  },
  validations() {
    return {}
  }
};
</script>

<style>
.theme-selector-input {
  width: 100%;
}

.theme-selector-input ul {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(7rem, 1fr));
  gap: var(--spacing-2);
}

.theme-selector-input li {
  position: relative;
}

.theme-selector-input input {
  appearance: none;
  height: 0;
  opacity: 0;
  position: absolute;
  width: 0;
}

.theme-selector-input label {
  display: grid;
  background: var(--bg, var(--color-white));
  background-clip: padding-box;
  grid-template:
    "preview complementary"
    "details details"
    / auto 1fr;
  gap: 0 var(--spacing-2);
  color: var(--foreground, var(--color-black));
  padding: var(--spacing-3);
  cursor: pointer;
  border: 1px solid var(--color-border);
  border-radius: var(--rounded);
  box-shadow: inset 0 0 0 3px var(--color-white);
}

.theme-selector-input__preview {
  grid-area: preview;
  height: var(--text-5xl);
  font-size: var(--text-5xl);
  line-height: var(--text-5xl);
  font-weight: var(--font-bold);
}

.theme-selector-input__preview svg {
  height: var(--text-4xl);
  width: var(--text-4xl);
}

.theme-selector-input__theme-details {
  align-items: center;
  display: flex;
  gap: var(--spacing-2);
  grid-area: details;
  margin-top: var(--spacing-2);
  padding-right: var(--spacing-5);
}

.theme-selector-input__indicator {
  height: .875rem;
  width: .875rem;
  border-radius: 100px;
  border: 1.5px solid;
  display: grid;
  place-items: center;
  flex: none;
}

.theme-selector-input input:focus + label {
  border: 1px solid var(--color-focus);
  box-shadow: inset 0 0 0 3px var(--color-white), 0 0 0 2px var(--color-focus-outline);
}

.theme-selector-input input:checked + label .theme-selector-input__indicator::before {
  content: "";
  width: 5px;
  height: 5px;
  background: currentColor;
  border-radius: 100px;
}

.theme-selector-input__theme-name {
  font-size: var(--text-xxs);
  font-weight: var(--font-normal);
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.theme-selector-input__complementary {
  align-self: end;
  display: grid;
  gap: .375rem;
  padding: var(--spacing-1) var(--spacing-1) .375rem;
}

.theme-selector-input__swatch {
  display: block;
  border-radius: 10em;
  height: .625rem;
  width: .625rem;
  background: var(--swatch);
}
</style>
