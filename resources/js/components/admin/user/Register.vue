<template>
    <b-modal id="register" title="사원등록" v-on:close="formReset()">
        <b-form>
            <b-form-group label="사번" label-for="employee_no">
                <b-form-input id="employee_no" v-model="form.employee_no" disabled></b-form-input>
            </b-form-group>
            <b-form-group label="이름" label-for="name">
                <b-form-input id="name" v-model="$v.form.name.$model" aria-describedby="input-name-feedback" required></b-form-input>
                <b-form-invalid-feedback id="input-name-feedback" :state="!$v.form.name.$error">이름을 입력해 주세요.</b-form-invalid-feedback>
            </b-form-group>
            <b-form-group label="주 근무지" label-for="branch">
                <b-form-select v-model="$v.form.branch_id.$model" :options="branches" aria-describedby="input-branch-feedback" required></b-form-select>
                <b-form-invalid-feedback id="input-branch-feedback" :state="!$v.form.branch_id.$error">근무지를 선택해 주세요.</b-form-invalid-feedback>
            </b-form-group>
            <b-form-group label="연락처" label-for="phone">
                <b-form-input id="phone" v-model="$v.form.phone.$model" aria-describedby="input-phone-feedback" required></b-form-input>
                <b-form-invalid-feedback id="input-phone-feedback" :state="!$v.form.phone.$error">전화번호를 형식에 맞게 입력해 주세요.</b-form-invalid-feedback>
            </b-form-group>
            <b-form-group label="이메일" label-for="email">
                <b-form-input id="email" v-model="$v.form.email.$model" aria-describedby="input-email-feedback" required></b-form-input>
                <b-form-invalid-feedback id="input-email-feedback" :state="!$v.form.email.$error">이메일을 이메일 형식에 맞게 입력해 주세요.</b-form-invalid-feedback>
            </b-form-group>
            <b-form-group label="비밀번호" label-for="password">
                <b-form-input id="password" v-model="$v.form.password.$model" aria-describedby="input-password-feedback" required></b-form-input>
                <b-form-invalid-feedback id="input-password-feedback" :state="!$v.form.password.$error">비밀번호를 입력해 주세요(8자 이상).</b-form-invalid-feedback>
            </b-form-group>
        </b-form>
        <template #modal-footer>
            <b-button variant="outline-danger" @click="closeModal">
                <b-icon icon="x"></b-icon>
                취소
            </b-button>
            <b-button variant="outline-primary" @click="create">
                <b-icon icon="check2"></b-icon>
                저장
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import { required, email, minLength, maxLength } from '@vuelidate/validators';
import { useVuelidate } from '@vuelidate/core';

export default {
    name: 'Register',

    mounted() {
        this.generateEmployeeNo();
        this.getBranches();
    },

    data() {
        return {
            form: {
                employee_no: '',
                name: '',
                branch_id: '',
                phone: '',
                email: '',
                password: '',
            },

            branches: null,
        }
    },

    setup: () => ({ $v: useVuelidate() }),

    validations() {
        return {
            form: {
                name: { required },
                branch_id: { required },
                phone: { required, maxLength: maxLength(11) },
                email: { required, email },
                password: { required, minLength: minLength(8) },
            }
        }
    },

    methods: {
        async generateEmployeeNo() {
            try {
                const response = await axios.get('/api/admin/generate_employee_no');

                this.form.employee_no = response.data;
            } catch (e) {
                this.showError(e);
            }
        },

        async getBranches() {
            try {
                const response = await axios.get('/api/branches');

                this.branches = response.data.data.map((item) => {
                    return {
                        value: item.id,
                        text: item.name,
                    };
                });
            } catch (e) {
                this.showError(e);
            }
        },

        formReset() {
            this.form.name = '';
            this.form.branch_id = '';
            this.form.phone = '';
            this.form.email = '';
            this.form.password = '';
        },

        showError(event) {
            this.$swal({
                icon: 'error',
                text: event.response.data.message || event.message,
            });
        },

        async create() {
            this.$v.$validate();

            try {
              await axios.post('/api/user', JSON.stringify(this.form));
            } catch (e) {
                this.showError(e);
            }
        },

        closeModal() {
            this.formReset();
            this.$v.form.$reset();
            this.$bvModal.hide('register');
        }
    }
}
</script>
