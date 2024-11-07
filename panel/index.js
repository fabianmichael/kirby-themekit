/* global panel */


import View from "./components/Views/View.vue";
import ThemesView from "./components/Views/ThemesView.vue";

import ThemeSelectorInput from "./components/Fields/ThemeSelectorInput.vue";
import ThemeSelectorField from "./components/Fields/ThemeSelectorField.vue";
import ThemeEditableFieldPreview from "./components/Previews/ThemeEditableFieldPreview.vue";
import ThemeReadabilityFieldPreview from "./components/Previews/ThemeReadabilityFieldPreview.vue";

panel.plugin('fabianmichael/themes', {
  components: {
    "k-theme-selector-input": ThemeSelectorInput,
    "k-themes-view": View,
    "k-themes-themes-view": ThemesView,
    "k-theme-editable-field-preview": ThemeEditableFieldPreview,
    "k-theme-readability-field-preview": ThemeReadabilityFieldPreview,
  },
  fields: {
    "theme-selector": ThemeSelectorField,
  },
})
