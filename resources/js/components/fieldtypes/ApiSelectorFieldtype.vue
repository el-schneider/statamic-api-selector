<template>
    <v-select
        ref="input"
        append-to-body
        :name="name"
        :clearable="config.clearable"
        :disabled="config.disabled || isReadOnly"
        :options="options"
        :placeholder="config.placeholder"
        :searchable="config.searchable"
        :multiple="config.multiple"
        :close-on-select="true"
        :value="selectedOptions"
        @input="vueSelectUpdated"
        @search:focus="$emit('focus')"
        @search:blur="$emit('blur')"
    >
        <template #selected-option-container v-if="config.multiple"
            ><i class="hidden"></i
        ></template>
        <template #selected-option="{ label, thumbnailUrl }">
            <div class="flex items-center">
                <img
                    v-if="!!thumbnailUrl"
                    class="w-5 h-5 mr-2"
                    :src="thumbnailUrl"
                />{{ label }}
            </div>
        </template>
        <template #search="{ events, attributes }" v-if="config.multiple">
            <input
                :placeholder="config.placeholder"
                class="vs__search"
                type="search"
                v-on="events"
                v-bind="attributes"
            />
        </template>
        <template #option="{ thumbnailUrl, label }">
            <span class="flex items-center">
                <img
                    v-if="!!thumbnailUrl"
                    class="w-12 h-12"
                    :src="thumbnailUrl"
                />
                <span class="ml-2">{{ label }}</span>
            </span>
        </template>
        <template #no-options>
            <div
                class="text-sm text-grey-70 text-left py-1 px-2"
                v-text="__('No options to choose from.')"
            />
        </template>
        <template #footer="{ deselect }" v-if="config.multiple">
            <div class="vs__selected-options-outside flex flex-wrap">
                <span
                    v-for="option in selectedOptions"
                    :key="option.value"
                    class="vs__selected mt-1"
                >
                    {{ option.label }}
                    <button
                        @click="deselect(option)"
                        type="button"
                        :aria-label="__('Deselect option')"
                        class="vs__deselect"
                    >
                        <span>×</span>
                    </button>
                </span>
            </div>
        </template>
    </v-select>
</template>

<script>
export default {
    mixins: [Fieldtype],

    data: function () {
        return {
            loading: true,
            options: this.meta.options,
        };
    },

    computed: {
        selectedOptions() {
            let selections = this.value || [];

            if (
                typeof selections === "string" ||
                typeof selections === "number"
            ) {
                selections = [selections];
            }

            return selections.map((value) => {
                return (
                    _.findWhere(this.options, { value }) || {
                        value,
                        label: value,
                    }
                );
            });
        },
        replicatorPreview() {
            return this.selectedOptions
                .map((option) => option.label)
                .join(", ");
        },
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },

        vueSelectUpdated(value) {
            if (this.config.multiple) {
                this.update(value.map((v) => v.value));
            } else {
                if (value) {
                    this.update(value.value);
                } else {
                    this.update(null);
                }
            }
        },
    },
};
</script>
