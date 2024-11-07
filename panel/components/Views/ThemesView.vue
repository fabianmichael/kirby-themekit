<script>
import CollectionView from "./CollectionView.vue";

export default {
	extends: CollectionView,
	data() {
		return {
			sortBy: null,
		};
	},
	computed: {
		buttons() {
			return [
				{
					icon: "add",
					text: this.$t("add"),
					click: () => this.$drawer("themes/create"),
				},
			];
		},
		columns() {
			return {
				title: {
					label: 'Title',
					type: "title",
					mobile: true,
          width: '6/20',
          html: true,
				},
				slug: {
					label: 'Slug',
					type: "slug",
          width: '6/20',
				},
        background: {
          label: 'Background',
          type: 'color',
          align: 'center',
          mobile: true,
          width: '2/20',
        },
        foreground: {
          label: 'Foreground',
          type: 'color',
          mobile: true,
          align: 'center',
          width: '2/20',
        },
        readability: {
          label: 'Contrast',
          type: 'theme-readability',
          align: 'center',
          width: '2/20',
        },
        editable: {
          label: ' ',
          mobile: true,
          align: 'center',
          type: 'theme-editable',
        }
			};
		},
		empty() {
			return {
				icon: "palette",
				text: this.$t("themes.empty"),
			};
		},
	},
	methods: {
		onCell({ row, columnIndex }) {
      if (!row.editable) {
        return
      }

			this.$drawer(`themes/${row.slug}/edit`, {
				query: {
					column: columnIndex,
				},
			});
		},
		options(theme) {
			return [
				{
					text: this.$t("edit"),
					icon: "edit",
          disabled: !theme.editable,
					click: () =>
						this.$drawer(`themes/${theme.slug}/edit`),
				},
				{
					text: this.$t("remove"),
					icon: "trash",
          disabled: !theme.editable,
					click: () =>
						this.$dialog(`themes/${theme.slug}/delete`),
				},
			];
		},
	},
};
</script>
