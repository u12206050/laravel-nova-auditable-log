<template>
    <div>
        <hr class="border-t-2 border-50 my-11">

        <div v-if="displayAudits">
            <h2 class="mb-3 text-90 font-normal text-2xl">{{__('Audit Log')}}</h2>
            <div class="card">
                <table cellpadding="0" cellspacing="0" data-testid="resource-table" class="table w-full">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="text-left"><span> {{__('User')}} </span></th>
                        <th class="text-left"><span> {{__('Event')}} </span></th>
                        <th class="text-left"><span> {{__('Date/Time')}} </span></th>
                        <th class="text-left"><span> {{__('Old Values')}} </span></th>
                        <th class="text-left"><span> {{__('New Values')}} </span></th>
                        <th v-if="canRestore"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="audit in audits" :key="audit.id">
                        <td>
                            <svg v-if="audit.event === 'created'" aria-hidden="true" focusable="false" data-prefix="fas"
                                 data-icon="save" class="h-6 text-60 svg-inline--fa fa-save fa-w-14" role="img"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"></path>
                            </svg>
                            <svg v-if="audit.event === 'updated'" aria-hidden="true" focusable="false" data-prefix="fas"
                                 data-icon="save" class="h-6 text-60 svg-inline--fa fa-save fa-w-14" role="img"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"></path>
                            </svg>
                            <svg v-if="audit.event === 'deleted'" aria-hidden="true" focusable="false" data-prefix="fas"
                                 data-icon="trash-alt" class="h-6 text-60 svg-inline--fa fa-trash-alt fa-w-14"
                                 role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="currentColor"
                                      d="M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128H32zm272-256a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zM432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16z"></path>
                            </svg>
                        </td>
                        <td>
                            {{ audit.user ? audit.user.name : __('console') }}
                        </td>
                        <td>
                            {{ audit.event }}
                        </td>
                        <td>
                            {{ audit.created_at }}
                        </td>
                        <td>
                            <div v-for="old_value in formatData(audit.old_values)" :key="old_value.name" class="my-2">
                                <span class="inline-block bg-30 p-1 rounded-sm mr-2">{{ old_value.name }}</span> {{
                                old_value.value }}
                            </div>
                        </td>
                        <td>
                            <div v-for="new_value in formatData(audit.new_values)" :key="new_value.name" class="my-2">
                                <span class="inline-block bg-30 p-1 rounded-sm mr-2">{{ new_value.name }}</span> {{
                                new_value.value }}
                            </div>
                        </td>
                        <td class="text-center" v-if="canRestore">
                            <svg @click="showRestoreAudit(audit)" width="100" height="100" style="max-width: 20px;" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M92.7143 49.8571C92.7143 55.6607 91.5796 61.2038 89.3103 66.4866C87.0409 71.7693 83.9903 76.3266 80.1585 80.1584C76.3266 83.9903 71.7693 87.0409 66.4866 89.3102C61.2039 91.5796 55.6607 92.7142 49.8571 92.7142C43.1979 92.7142 36.9386 91.3005 31.0792 88.4732C25.2199 85.6458 20.2719 81.6837 16.2355 76.587C12.199 71.4903 9.45536 65.6867 8.00446 59.1763C7.89286 58.6555 8.02307 58.1532 8.39509 57.6696C8.72991 57.2232 9.19494 56.9999 9.79018 56.9999H20.8951C21.7507 56.9999 22.3088 57.4278 22.5692 58.2834C24.4293 64.3102 27.8705 69.1744 32.8929 72.8761C37.9152 76.5777 43.5699 78.4285 49.8571 78.4285C53.7262 78.4285 57.4185 77.6752 60.9342 76.1685C64.4498 74.6618 67.4911 72.6249 70.058 70.058C72.625 67.491 74.6618 64.4497 76.1685 60.9341C77.6752 57.4185 78.4286 53.7261 78.4286 49.8571C78.4286 45.988 77.6752 42.2957 76.1685 38.7801C74.6618 35.2645 72.625 32.2232 70.058 29.6562C67.4911 27.0892 64.4498 25.0524 60.9342 23.5457C57.4185 22.039 53.7262 21.2857 49.8571 21.2857C46.2113 21.2857 42.7143 21.946 39.3661 23.2667C36.0179 24.5874 33.0417 26.4754 30.4375 28.9308L38.0826 36.6316C39.2359 37.7477 39.4963 39.0312 38.8638 40.4821C38.2314 41.9702 37.1339 42.7142 35.5714 42.7142H10.5714C9.60417 42.7142 8.76711 42.3608 8.06027 41.654C7.35342 40.9471 7 40.1101 7 39.1428V14.1428C7 12.5803 7.74405 11.4828 9.23214 10.8504C10.683 10.218 11.9665 10.4784 13.0826 11.6316L20.3371 18.8303C24.3177 15.0729 28.8657 12.1618 33.981 10.097C39.0964 8.03231 44.3884 6.99995 49.8571 6.99995C55.6607 6.99995 61.2039 8.13462 66.4866 10.404C71.7693 12.6733 76.3266 15.7239 80.1585 19.5558C83.9903 23.3876 87.0409 27.9449 89.3103 33.2276C91.5796 38.5104 92.7143 44.0535 92.7143 49.8571Z" fill="black"/>
                            </svg>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="bg-20 rounded-b" per-page="5" resource-count-label="1-3 of 3" current-resource-count="3"
                     all-matching-resource-count="3">
                    <nav class="flex justify-between items-center">
                        <button :disabled="pagination.prev_page_url === null" rel="prev" dusk="previous"
                                class="btn btn-link py-3 px-4 text-80"
                                :class="{ 'opacity-50': pagination.prev_page_url === null, 'text-primary': pagination.prev_page_url !== null }"
                                @click="fetchAudits(pagination.prev_page_url)">
                            {{__('Previous')}}
                        </button>
                        <span class="text-sm text-80 px-4">
                    {{ pagination.from }}-{{ pagination.to }} of {{ pagination.total }}
                </span>
                        <button :disabled="pagination.next_page_url === null" rel="next" dusk="next"
                                :class="{ 'opacity-50': pagination.next_page_url === null, 'text-primary': pagination.next_page_url !== null }"
                                class="btn btn-link py-3 px-4 text-80" @click="fetchAudits(pagination.next_page_url)">
                            {{__('Next')}}
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <button class="btn btn-default btn-primary" @click.prevent="showAndFetch" v-if="displayAudits === false">
            {{__('View Audit Log')}}
        </button>

        <restore-audit-modal v-if="restore !== null" :fields="parentFields" :resourceName="resourceName"
                             :resourceId="resourceId" :audit="restore" @close="restore = null" @restored="restored">
        </restore-audit-modal>
    </div>
</template>

<script>
    import RestoreAuditModal from './RestoreAuditModal';
    import { normaliseFields } from './../fields';

    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],

        components: {
            RestoreAuditModal
        },
        data() {
            return {
                audits: [],
                displayAudits: false,
                pagination: {},
                restore: null,
                parentFields: [],
                canRestore: false,
            }
        },

        mounted() {
            if (this.displayAudits === true) {
                this.fetchAudits();
            }

            // Normalise the parent fields
            this.parentFields = normaliseFields(this.resource.fields);
        },

        methods: {

            showAndFetch() {
                this.displayAudits = true;
                this.fetchAudits();
            },

            async fetchAudits(page = null) {
                if (!page) {
                    page = `/nova-vendor/auditable-log/audits/${this.resourceName}/${this.resourceId}`
                }

                try {
                    const {data} = await Nova.request().get(page);
                    this.audits = data.audits.data;
                    this.pagination = data.audits;
                    this.canRestore = data.restore;
                }
                catch(e) {
                    // Do nothing, nova handles errors
                }
            },

            formatData(values) {
                let returned = [];

                for (var property in values) {
                    if (values.hasOwnProperty(property)) {
                        returned.push({name: property, value: values[property]});
                    }
                }

                return returned;
            },

            showRestoreAudit(audit) {
                this.restore = audit;
            },

            restored(updatedProps) {
                // Updates the value of the parent fields
                updatedProps.forEach(prop => {
                    this.$set(this.parentFields[prop.key], 'value', prop.value);
                });

                // Reload resource
                let parent = this.$parent
                while(!parent.getResource) parent = parent.$parent

                parent.getResource()
            }
        }
    }
</script>
