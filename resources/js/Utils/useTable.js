import { h, reactive, resolveComponent, computed, ref, createVNode } from "vue";
import { router } from '@inertiajs/vue3'
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { Modal } from "ant-design-vue";

/**
 * Traits of using table
 *
 * @param binding data source
 * @param options
 *
 * options.fieldName
 * options.selectable
 */
export default function (binding, options) {
    const reserved = ["keyword", "sort", "order", "page", "size"];

    const filtered = ref({});
    const sorted = ref({});

    const loading = ref(false);

    const items = ref(binding.data);

    const selectedRowKeys = reactive([]);

    let url = new URL(decodeURIComponent(window.location.href));

    // reconstruct filters from url
    const syncColumnFilters = () => {
        const filters = {};

        for (const [param, value] of url.searchParams) {
            if (param.startsWith("filter")) {
                const key = param.replace("filter[", "").replace("]", "");
                filters[key] = value.split(",");
            }
        }

        filtered.value = filters;
    };

    // reconstruct sort from url
    const syncColumnSorter = () => {
        if (url.searchParams.has("sort")) {
            const sort = url.searchParams.get("sort");
            const isDesc = sort.charAt(0) === "-";

            sorted.value = {
                columnKey: isDesc ? sort.substring(0, 1) : sort,
                order: isDesc ? "descend" : "ascend",
            };
        }
    };

    syncColumnFilters();
    syncColumnSorter();

    const pagination = ref({
        total: binding.total,
        current: binding.current_page,
        pageSize: binding.per_page,
        showSizeChanger: true,
        hideOnSinglePage: true,
    });

    const setPagination = (data) => {
        console.log(data);
        pagination.value.total = data.total;
        pagination.value.current = data.current_page;
        pagination.value.pageSize = data.per_page;
    };

    const updateData = (url, payload) => {
        loading.value = true;
        router.get(url, payload, {
            preserveState: true,
            onSuccess: (page) => {
                loading.value = false;
                setPagination(page.props[options.fieldName]);
                items.value = page.props[options.fieldName].data;
            },
        });
    };

    const onPaginationChange = (page, pageSize) => {
        loading.value = true;

        updateData(url.href, {
            ...(page && { page: page }),
            ...(pageSize && { per_page: pageSize }),
        });
    };

    pagination.value.onChange = onPaginationChange;
    pagination.value.onShowSizeChange = onPaginationChange;

    const search = reactive({
        keyword: url.searchParams.get("keyword") ?? "",
        apply(keyword) {
            loading.value = true;
            url = new URL(window.location.href);
            url.search = "";
            updateData(url.href, {
                ...(keyword && { keyword: keyword }),
            });
        },
    });

    const compileFilter = (filters) => {
        const queryParams = {};
        Object.keys(filters).forEach((key) => {
            const newKey = "filter[" + key + "]";

            switch (typeof filters[key]) {
                case "boolean":
                    queryParams[newKey] = filters[key] ? "true" : "false";
                    break;
                case "object":
                    queryParams[newKey] = filters[key].join(",");
                    break;
                default:
                    queryParams[newKey] = filters[key];
            }

            if (queryParams[newKey] === "") {
                delete queryParams[newKey];
            }
        });

        return queryParams;
    };

    const compileSorter = (sorter) => {
        console.debug("compileSorter", sorter);
        if (!sorter.order || !sorter.field) {
            return {};
        }

        const order = sorter.order === "ascend" ? "" : "-";

        return { sort: `${order}${sorter.field}` };
    };

    const navigateToPage = (payload = {}) => {
        url.search = "";
        loading.value = true;
        // console.log(pagination.value)
        updateData(url.href, {
            ...compileFilter(filtered.value),
            ...compileSorter(sorted.value),
            ...payload,
            // omit default page size and first page
            ...(pagination.value.current &&
                pagination.value.pageSize !== 1 && { page: pagination.value.current }),
            ...(pagination.value.pageSize &&
                pagination.value.pageSize !== 10 && {
                per_page: pagination.value.pageSize,
            }),
            ...(search.keyword && { keyword: search.keyword }),
        });
    };

    const change = (pag, criteria, sorter) => {
        url.search = "";

        // filtered.value = criteria;
        sorted.value = sorter;
        pagination.value = pag;
        loading.value = true;

        router.get(
            url.href,
            {
                ...compileFilter(filtered.value),
                ...compileSorter(sorter),
                ...(pag.current && { page: pag.current }),
                ...(pag.pageSize !== 10 && { per_page: pag.pageSize }),
                ...(search.keyword && { keyword: search.keyword }),
            },
            {
                preserveState: true,
                onSuccess: (page) => {
                    loading.value = false;
                    setPagination(page.props[options.fieldName]);
                    items.value = page.props[options.fieldName].data;
                },
            }
        );
    };

    const applyFilter = () => {
        pagination.value.current = 1;
        navigateToPage();
    };

    const resetFilter = () => {
        pagination.value.current = 1;
        filtered.value = {};
        navigateToPage();
    };

    const applyRangeFilter = (field, value) => {
        pagination.value.current = 1;
        sorted.value = {};

        const payload = {};
        payload[field] = value.join(":");

        navigateToPage(payload);
    };

    const onSelectChange = (newKeys) => {
        selection.selectedRowKeys = newKeys;
    };

    const selection = reactive({
        selectedRowKeys: [],
        onChange: onSelectChange,
        clear() {
            selection.selectedRowKeys = [];
        },
        selected: computed(() => {
            return selection.selectedRowKeys.length !== 0;
        }),
        count: computed(() => {
            return selection.selectedRowKeys.length;
        }),
        destroy(handler) {
            Modal.confirm({
                title: "Are you sure to delete these recordsasldasldasd?",
                icon: createVNode(ExclamationCircleOutlined),
                content: "You will not be able to recover these records!",
                okText: "Yes",
                cancelText: "No",
                onOk: handler,
            });
        },
    });

    const navigate = (params) => {
        router.replace(route(route().current(), params));
    };

    const action = {
        // TODO: fixme
        remove(id) {
            // pass redirection,
            // redirect
            // if last page item of page, go to previous page
            // if first page last item, refresh page only
            navigate(params);
        },
    };

    return reactive({
        ...options,
        rowSelection: options.selectable ? selection : null,
        rowKey: options.rowKey ?? "id",
        scroll: { x: true },
        pagination,
        search,
        filtered,
        sorted,
        change,
        action,
        loading,
        reload: navigateToPage,
        applyFilter,
        resetFilter,
        applyRangeFilter,
        dataSource: items,
    });
}