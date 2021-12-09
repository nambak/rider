<template>
    <div>
        <b-card :header="state" :header-bg-variant="stateColor" header-text-variant="white" class="mb-5">
            <b-card-text>
                <p>주문번호: {{ data.order_id }}</p>
                <p>주문일: {{ data.order_date }}</p>
                <p>고객연락처: {{ data.buyer_cellphone }}</p>
                <p>주소: {{ data.receiver_address_full }}</p>
                <p>메세지: {{ data.shipping_message }}</p>

                <b-row>
                    <b-col class="text-center">
                        <b-overlay
                            :show="isLoading"
                            rounded
                            opacity="0.6"
                            spinner-small
                            spinner-variant="primary"
                            class="d-inline-block"
                            @hidden="onHidden"
                        >
                            <b-button variant="primary" @click="startDelivery" ref="actionButton" :disabled="isLoading">{{ actionName }}</b-button>
                        </b-overlay>
                    </b-col>
                </b-row>
            </b-card-text>
        </b-card>
        <b-row>
            <b-col class="text-center">
                <b-button variant="warning">
                    <a :href="kakaoMapLink" target="_blank">카카오 맵 경로찾기</a>
                </b-button>
            </b-col>
        </b-row>

    </div>
</template>

<script>
export default {
    name: 'MyOrders',

    props: ['order'],

    data() {
        return {
            data: [],
            state: '픽업완료',
            kakaoMapLink: '',
            actionName: '배송시작',
            isLoading: false,
        }
    },

    async mounted() {
        try {
            const response = await axios.get(`/api/order/${this.order.order_id}`);

            if (response.status === 200) {
                this.data = response.data;
                this.getGeoLocation();
                this.setAction();
            }
        } catch (e) {
            this.$swal({
                icon: 'error',
                title: '오류',
                text: e.message
            });
        }
    },

    methods: {
        onHidden() {
            this.$refs.actionButton.focus();
        },
        startDelivery() {
            if (this.actionName === '배송시작') {
                this.createCafe24Shipment();
            } else if (this.actionName === '배송완료') {
                location.href = `/order/${this.order.id}/delivery_complete`;
            }
        },

        async createCafe24Shipment() {
            this.isLoading = true;

            try {
                const response = await axios.post(`https://deliver.10tenminute.xyz/api/persist_shipment`, {
                    'order_number': this.order.order_id,
                });

                if (response.status === 200) {
                    axios.post(`/api/order/${this.order.id}/start_delivery`);
                    this.state = '배송중';
                }
            } catch (e) {
                this.$swal({
                    icon: 'error',
                    title: '오류',
                    text: e.message,
                });
            } finally {
                this.isLoading = false;
                location.reload();
            }
        },

        async getGeoLocation() {
            let geocoder = new kakao.maps.services.Geocoder();

            geocoder.addressSearch(this.data.delivery.address1, (result, status)  => {
                console.log(result);
                if (status === kakao.maps.services.Status.OK) {
                    this.kakaoMapLink =
                        'https://map.kakao.com/link/to/'
                        + result[0].address_name + ','
                        + result[0].y + ','
                        + result[0].x;
                }
            });
        },

        setAction() {
            if (this.data) {
                if (this.data.delivery.started_at === null) {
                    this.actionName = '배송시작';
                } else {
                    this.actionName = '배송완료';
                }
            }
        }
    },

    computed: {
        stateColor() {
            switch (this.state) {
                case '팍업완료':
                    return 'success';
                case '배송중':
                    return 'primary';
                default:
                    return 'success';
            }
        },
    }
}
</script>
