<template>
    <div>
        <b-card title="사용자 관리">
            <b-row class="mb-2">
                <b-col>
                    <b-input-group>
                        <b-select :options="options" v-model="searchField"></b-select>
                        <b-form-input v-model="searchKeyword" class="w-50" placeholder="검색어를 입력해 주세요."></b-form-input>
                        <b-input-group-append>
                            <b-button variant="primary">
                                <b-icon icon="search"></b-icon>
                                검색
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col></b-col>
                <b-col class="text-right">
                    <b-button variant="outline-primary">
                        <b-icon icon="person-plus-fill"></b-icon>
                        사번생성
                    </b-button>
                    <b-button variant="outline-danger">
                        <b-icon icon="trash"></b-icon>
                        선택삭제
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-table
                        :fields="fields"
                        head-variant="dark"
                        striped
                        hover
                        bordered
                        show-empty
                        empty-text="등록된 사용자가 없습니다."
                    >
                        <template #head(selected)="data">
                            <b-form-checkbox
                                @click.native.stop
                                @change="selectAll"
                                v-model="allSelected"
                            ></b-form-checkbox>
                        </template>
                        <template #cell(selected)="row">
                            <b-form-checkbox
                                @click.native.stop
                                :value="data.item.schoolId"
                                :checked="allSelected"
                                v-model="selected"
                            ></b-form-checkbox>
                            {{ data.index }}
                        </template>
                    </b-table>
                    <b-pagination
                        v-model="currentPage"
                        :total-rows="totalRows"
                        :per-page="perPage"
                        align="center"
                    ></b-pagination>
                </b-col>
            </b-row>
        </b-card>
    </div>
</template>

<script>
export default {
    name: 'List',

    data() {
        return {
            fields: [
                { key: 'selected', label: '', sortable: false, class: 'text-center' },
                { key: 'employee_no', label: 'ID', sortable: false, class: 'text-center' },
                { key: 'name', label: '이름', sortable: false, class: 'text-center' },
                { key: 'branch_name', label: '배정근무지', sortable: false, class: 'text-center' },
                { key: 'phone', label: '연락처', sortable: false, class: 'text-center' },
                { key: 'email', label: '이메일', sortable: false, class: 'text-center' },
                { key: 'action', label: '', sortable: false, class: 'text-center' }
            ],
            options: [
                { text: 'ID', value: 'employee_no', },
                { text: '이름', value: 'name', },
            ],
            searchField: 'employee_no',
            searchKeyword: null,
            select: null,
            allSelected: false,
            selected: null,
            totalRows: null,
            currentPage: 1,
            perPage: 25,
        }
    },

    methods: {
        selectAll() {
            return true;
        },
    }
}
</script>
