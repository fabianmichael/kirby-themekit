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
  font-size: var(--text-xs);
  font-weight: var(--font-normal);
  display: block;
  overflow: hidden;
  padding-block: .2em;
  margin-block: -.2em;
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
