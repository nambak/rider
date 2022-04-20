<template>
    <div>
        <b-card title="사용자 관리">
            <b-row class="mb-2">
                <b-col>
                    <b-input-group>
                        <b-select :options="options" v-model="searchField"></b-select>
                        <b-form-input v-model="searchKeyword" class="w-50" placeholder="검색어를 입력해 주세요."></b-form-input>
                        <b-input-group-append>
                            <b-button variant="primary" @click="getList()">
                                <b-icon icon="search"></b-icon>
                                검색
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-col>
                <b-col></b-col>
                <b-col class="text-right">
                    <b-button variant="outline-primary" v-b-modal.register>
                        <b-icon icon="person-plus-fill"></b-icon>
                        사원등록
                    </b-button>
                    <b-button variant="outline-danger" @click="deleteEmployee()">
                        <b-icon icon="trash"></b-icon>
                        선택삭제
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-table
                        :fields="fields"
                        :items="employees"
                        head-variant="dark"
                        ref="employeeList"
                        striped
                        hover
                        bordered
                        show-empty
                        selectable
                        select-mode="multi"
                        @row-selected="onRowSelected"
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
                            <b-checkbox v-model="row.rowSelected" @change="onRowCheckboxSelected(row)"></b-checkbox>
                        </template>
                        <template #cell(action)="data">
                            <b-button
                                variant="link"
                                v-b-modal.edit
                                @click="editEmployee = data.item"
                            >
                                <b-icon icon="pencil"></b-icon> 수정
                            </b-button>
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
        <user-register id="register" title="사원 등록"></user-register>
        <user-edit id="edit" title="사원 정보 수정":employee="this.editEmployee"></user-edit>
    </div>
</template>

<script>
import UserRegister from "./Register";
import UserEdit from './Edit';

export default {
    name: 'List',

    components: { UserRegister, UserEdit },

    mounted() {
        this.getList();
    },

    data() {
        return {
            fields: [
                { key: 'selected', label: '', sortable: false, class: 'text-center align-middle' },
                { key: 'employee_no', label: 'ID', sortable: false, class: 'text-center align-middle' },
                { key: 'name', label: '이름', sortable: false, class: 'text-center align-middle' },
                { key: 'branch_name', label: '배정근무지', sortable: false, class: 'text-center align-middle' },
                { key: 'phone', label: '연락처', sortable: false, class: 'text-center align-middle' },
                { key: 'email', label: '이메일', sortable: false, class: 'text-center align-middle' },
                { key: 'action', label: '', sortable: false, class: 'text-center align-middle' }
            ],
            options: [
                { text: 'ID', value: 'employee_no', },
                { text: '이름', value: 'name', },
            ],
            searchField: 'employee_no',
            searchKeyword: null,
            select: null,
            allSelected: false,
            selected: [],
            totalRows: null,
            currentPage: 1,
            perPage: 25,
            employees: null,
            editEmployee: null,
        }
    },

    methods: {
        selectAll() {
            if (this.allSelected) {
                this.$refs.employeeList.selectAllRows();
            } else {
                this.$refs.employeeList.clearSelected()
            }
        },

        async getList() {
            try {
                const response = await axios.get('/api/employees', {
                    params: {
                        per_page: this.perPage,
                        search_field: this.searchField,
                        search_keyword: this.searchKeyword,
                    }
                });

                this.employees = response.data.data;
                this.totalRows = response.data.total;
                this.currentPage = response.data.current_page;

            } catch (e) {
                this.showError(e);
            }
        },

        onRowSelected(items) {
            this.selected = items;
        },

        onRowCheckboxSelected(row) {
            if (row.rowSelected) {
                this.$refs.employeeList.selectRow(row.index);
            } else {
                this.$refs.employeeList.unselectRow(row.index);
            }
        },

        async deleteEmployee() {
            if (!this.selected.length) {
                this.$swal({
                    icon: 'error',
                    text: '삭제할 사원을 선택하지 않았습니다.'
                });

                return false;
            }

            try {
                await axios.delete('/api/employees', {
                    data: {
                        ids: this.selected.map((item) => { return item.user_id; })
                    }
                });

                this.$swal({
                    icon: 'success',
                    text: '삭제되었습니다.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.getList();
                    }
                });
            } catch (e) {
                this.showError(e);
            }
        },
    }
}
</script>
