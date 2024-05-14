<template>
    <div>
        <a-alert
            v-if="table.rowSelection?.selected"
            class="!mb-4"
        >
            <template #message>
                <div class="flex justify-between items-center">
                    <div>
                        <span>{{ $t('items_selected', {count: table.rowSelection?.count}) }}</span>&nbsp;
                        <a @click="table.rowSelection?.clear">
                            {{ $t('clear_selection') }}
                        </a>
                    </div>
                    <span class="gap-3 flex">
                        <a-button
                            v-if="bulkDestroyHandler && hasPermission('bulk-delete')"
                            type="link"
                            danger
                            @click="confirmBulkDestroy"
                        >
                            {{ $t('action.bulk-remove') }}
                        </a-button>

                        <slot name="bulkActions">
                        </slot>
                    </span>
                </div>
            </template>
        </a-alert>

        <a-table
            v-bind="table"
            @change="table.change"
            :class="tableClass"
            sticky
        >
            <template #bodyCell="{ column, record}">
                <slot name="bodyCell" :column="column" :record="record"></slot>
                <template v-if="column.dataIndex === 'action' || column.key === 'action'">
                    <slot name="rowActions"></slot>
                    <a-button
                        type="link"
                        v-if="viewHandler && hasPermission('view')"
                        @click="handleView(record)"
                    >
                        {{ $t('action.view') }}
                    </a-button>
                    <a-button
                        type="link"
                        v-if="editHandler && hasPermission('edit')"
                        @click="handleEdit(record)"
                    >
                        {{ $t('action.edit') }}
                    </a-button>
                    <a-popconfirm
                        v-if="deleteHandler && hasPermission('delete')"
                        title="Are you sure to delete this record?"
                        ok-text="Yes"
                        cancel-text="No"
                        @confirm="handleDelete(record)"
                    >
                        <a-button type="link" danger
                                  v-if="hasPermission('delete')"
                        >
                            {{ $t('action.remove') }}
                        </a-button>
                    </a-popconfirm>
                </template>
            </template>

            <template #footer v-if="table.pagination.total > 0">
                Showing {{ (table.pagination.current - 1) * table.pagination.pageSize + 1 }} to {{ entryCountUpper }} of {{ table.pagination.total }} entries
            </template>
        </a-table>
    </div>
</template>

<script>
import {ExclamationCircleOutlined} from "@ant-design/icons-vue"
import {Modal} from 'ant-design-vue'
import {createVNode} from 'vue'

export default {
    name: "CRUDTable",
    components: {
        ExclamationCircleOutlined,
    },
    props: {
        permissions: {
            type: Object,
            required: false,
        },
        table: {
            type: Object,
            required: true,
        },
        resource: {
            type: String,
            required: true,
        },
        // currently only accepts array params
        resourceParams: {
            type: Object,
            required: false,
            default: () => [],
        },
        checkPermission: {
            type: Boolean,
            default: true,
        },
        viewHandler: {
            type: [Function, Boolean],
            required: false,
            default: true,
        },
        editHandler: {
            type: [Function, Boolean],
            required: false,
            default: true,
        },
        deleteHandler: {
            type: [Function, Boolean],
            required: false,
            default: true,
        },
        bulkDestroyHandler: {
            type: [Function, Boolean],
            required: false,
            default: true,
        },
        tableClass: {
            type: [String, Function],
            required: false,
        }
    },
    // bind table and data
    computed: {
        hasPermission() {
            // view, delete, update, bulk-delete
            return (permission) => {
                if (!this.checkPermission) {
                    return true;
                }

                return this.$can(`${this.resource}.${permission}`)
            }
        },
        entryCountUpper() {
            return Math.min(this.table.pagination.current * this.table.pagination.pageSize, this.table.pagination.total)
        }
    },
    methods: {
        confirmBulkDestroy() {
            Modal.confirm({
                title: 'Are you sure to delete these records?',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'You will not be able to recover these records!',
                okText: 'Yes',
                cancelText: 'No',
                onOk: this.handleBulkDestroy,
            });
        },
        handleBulkDestroy () {
            if ('function' === typeof this.bulkDestroyHandler) {
                this.bulkDestroyHandler(record)
                return;
            }

            this.$inertia.delete(this.route(`${this.resource}.bulk-destroy`, this.resourceParams), {
                data: {
                    ids: this.table.rowSelection.selectedRowKeys
                },
                preserveState: false,
                onSuccess: (page) => {
                    this.table.rowSelection.clear();
                    if (page.props.message) {
                        this.$message.warning(page.props.message);
                        return
                    }
                    this.$message.success('Bulk delete success');
                }
            })
        },
        handleDelete(record) {
            if ('function' === typeof this.deleteHandler) {
                this.deleteHandler(record)
                return;
            }

            this.$inertia.delete(this.route(`${this.resource}.destroy`, [
                ...this.resourceParams,
                record.id
            ]), {
                preserveState: false,
                onSuccess: (page) => {
                    if (page.props.message) {
                        this.$message.warning(page.props.message);
                        return
                    }
                    this.$message.success('Operation success.');
                }
            })
        },
        handleEdit(record) {
            if ('function' === typeof this.editHandler) {
                this.editHandler(record)
                return;
            }
            this.$inertia.visit(route(`${this.resource}.edit`, [
                ...this.resourceParams,
                record.id
            ]));
        },
        handleView(record) {
            if ('function' === typeof this.viewHandler) {
                this.viewHandler(record)
                return;
            }
            this.$inertia.visit(route(`${this.resource}.show`, [
                ...this.resourceParams,
                record.id
            ]));
        }
    }
}
</script>

<style scoped>

</style>
