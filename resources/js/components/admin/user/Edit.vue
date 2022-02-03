<script>
import Register from './Register';
import {email, maxLength, minLength, required} from "@vuelidate/validators";

export default {
    name: 'Edit',

    extends: Register,

    props: ['employee'],

    validations() {
        return {
            form: {
                name: { required },
                branch_id: { required },
                phone: { required, maxLength: maxLength(11) },
                email: { required, email },
                password: { minLength: minLength(8) },
            }
        }
    },

    methods: {
        onShow() {
            this.form.employee_no = this.employee.employee_no;
            this.form.name = this.employee.name;
            this.form.branch_id = this.employee.branch_id;
            this.form.phone = this.employee.phone;
            this.form.email = this.employee.email;
        },

        async store() {
            this.$v.$validate();

            if (!this.$v.$error) {
                let data = {
                    name: this.form.name,
                    branch_id: this.form.branch_id,
                    phone: this.form.phone,
                    email: this.form.email,
                }

                if (this.form.password.length) {
                    data.password = this.form.password;
                }
                try {
                    await axios.put(`/api/employee/${this.employee.user_id}`, data);

                    this.$swal({
                        icon: 'success',
                        text: '수정되었습니다.'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.closeModal();
                        }
                    });
                } catch (e) {
                    this.showError(e);
                }
            }
        },
    }
}
</script>
