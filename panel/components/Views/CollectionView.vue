<template>
	<k-themes-view v-bind="$props" class="k-themes-collection-view">
		<template #buttons>
			<k-button
				v-for="(button, key) in buttons"
				v-bind="button"
				:key="key"
				size="sm"
				variant="filled"
			/>
		</template>

		<!-- Empty state -->
		<k-empty v-if="this.data.length === 0" v-bind="empty" layout="table" />

		<!-- Table -->
		<k-table
			v-else
			:columns="columns"
			:index="index"
			:rows="paginatedItems"
			@cell="onCell"
		>
			<template #options="{ row }">
				<k-options-dropdown :options="options(row)" />
			</template>
		</k-table>

		<footer class="k-bar k-collection-footer">
			<k-pagination
				v-bind="pagination"
				:details="true"
				:total="data.length"
				@paginate="pagination.page = $event.page"
			/>
		</footer>
	</k-themes-view>
</template>

<script>
import { props as View } from "./View.vue";

export default {
	mixins: [View],
	data() {
		return {
			searching: false,
			q: null,
			pagination: {
				page: 1,
				limit: 20,
			},
		};
	},
	computed: {
		buttons() {
			return [];
		},
		columns() {
			return {};
		},
		empty() {
			return {};
		},
		index() {
			return (this.pagination.page - 1) * this.pagination.limit + 1;
		},
		paginatedItems() {
			return this.data.slice(
				this.index - 1,
				this.pagination.limit * this.pagination.page,
			);
		},
	},
	methods: {
		onCell() {},
		options() {
			return [];
		},
		async toggleSearch(forgiving = false) {
			if (forgiving && this.q) {
				this.q = null;
				return;
			}

			this.q = null;
			this.searching = !this.searching;

			if (this.searching) {
				await this.$nextTick();
				this.$refs.search.focus();
			}
		},
	},
};

</script>

<style>

.k-themes-collection-view td.k-table-column {
	cursor: pointer;
}

.k-themes-collection-view .k-color-field-preview {
  justify-content: center;
}

</style>
