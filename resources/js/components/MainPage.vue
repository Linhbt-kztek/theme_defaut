<template>
    <div class="services-favourite__tab" id="services-favourite-tab">
        <div class="edit-change">
            <div class="item-change">
                <button class="change-maps" v-for="area in areas" @click="filterArea(area.id)">
                    <img class="icon-2" :src="imageForArea(area.id)" alt="">
                    <span>{{ area.name }}</span>
                </button>

                <!-- <i class="ri ri-arrow-left-s-line preService arrowClick" aria-hidden="true"></i>
                <i class="ri ri-arrow-right-s-line nextService arrowClick" aria-hidden="true"></i> -->

            </div>
        </div>


        <!-- <div v-if="!user_status" class="services-filter__right" id="click-date">
            <b>Ngày sử dụng</b>
            <VueDatePicker v-model="selectedDate" :format="format" :format-locale="vi" format="E">
            </VueDatePicker>
        </div> -->


    </div>
    <div class="services-list">
        <div class="item listTicket listTicket-0" v-for="ticket in filteredTickets" :key="ticket.id">

            <div class="item-img" style="text-align: center; height: 180px">
                <img v-bind:src="ticket.url_image" style="width:auto; height:100%; max-height: 200px"
                    title="Xem chi tiết" class="lazy show-content" alt="">

            </div>
            <div class="item-content">
                <div class="item-content__name">
                    <h3 title="Xem chi tiết" class="show-content">
                        <span class="title-tick title">{{ ticket.name }}</span>
                    </h3>
                </div>
                <div class="item-content__price">
                    <div class="pro-detail-icon show-content" @click="showDetail(ticket.id)">
                        <i class="ri ri-information-fill" aria-hidden="true"></i> Chi tiết
                    </div>
                    <div class="item-content__price-percen">
                        <div class="price-show" id="price_{{ ticket.id }}">{{ formatPrice(ticket.price_online) }} đ
                        </div>
                    </div>
                </div>
                <div class="item-content__qty">

                </div>
                <div class="item-content__button">
                    <div class="left">
                        <button class="button-qty onChangeBuy" @click="onMinusQty(ticket)">
                            <i class="ri ri-subtract-line" aria-hidden="true"></i>
                        </button>
                        <div class="qtyDiv">
                            <span class="qty-value qty-value">{{ getQuantity(ticket.id) }}</span>
                        </div>

                        <button class="button-qty onChangeBuy button-qty-plus" @click="onPlusQty(ticket)">

                            <i class="ri ri-add-line" aria-hidden="true"></i>
                        </button>
                    </div>
                    <span class="total-price-css total-price">
                        {{ totalPrice(getQuantity(ticket.id), ticket.price_online) }} đ</span>
                </div>

            </div>

        </div>

    </div>
    <div class="box-experience__gallery">
        <div class="item float-left" v-for="image in json_url_banner">
            <img class="item-img default lazy" style="object-fit: cover !important;"
                :src="image || '/images/noimage.jpg'" alt="">
        </div>
    </div>
    <div class="services-order" id="services-order-id">
        <div class="services-order__list">
            <div class="row">
                <div class="col-8">
                    <h3 class="title" style="">Thông tin chi tiết giỏ hàng</h3>
                </div>
                <div class="col-4">
                    <div v-if="!user_status" class="" id="click-date"
                        style="background-color: #8f2624;border-radius: 5px;color: #fff;">
                        <a style="padding-left: 10px;">Ngày sử dụng <span style="color: #fff;">*</span></a>
                        <VueDatePicker v-model="selectedDate" :format="format" :format-locale="vi" format="E">
                        </VueDatePicker>
                    </div>
                </div>
            </div>



            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th class="first-th"><strong>Tên vé</strong></th>
                            <th class="qty-cart"><strong>Số lượng</strong></th>
                            <th style="text-align: center;"><strong>Xóa</strong></th>
                            <th><strong>Đơn giá</strong></th>
                            <th><strong>Thành tiền</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="cartIDShow" v-for="(ticket, index) in cart" :key="index">
                            <td class="titleItem">{{ ticket.name }}</td>
                            <td>
                                <div class="divQtyCss">
                                    <button @click="onMinusQty(ticket)"
                                        class="button-qty float-left onChangeCart trash-cong-btn">
                                        <i class="ri ri-subtract-line" aria-hidden="true"></i>
                                    </button>
                                    <div class="qtyDiv">
                                        <span class="qty-value float-left qtyItemCart">{{ getQuantity(ticket.id)
                                            }}</span>
                                    </div>

                                    <button @click="onPlusQty(ticket)"
                                        class="button-qty float-left onChangeCart trash-cong-btn button-qty-plus">
                                        <i class="ri ri-add-line" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button style="font-size: 18px !important; color: rgba(48, 54, 68, 0.4);"
                                    @click="removeFromCart(index)" class="deleteCart">
                                    <i class="ri ri-delete-bin-7-fill" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>
                                <label class="font-default total-price-unit">{{ formatPrice(ticket.price) }}₫</label>
                            </td>
                            <td style="width: 150px;">
                                <label class="total-price">
                                    {{ totalPrice(getQuantity(ticket.id), ticket.price) }}₫
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="services-order__content checkCart">
            <div class="left">
                <!-- <div class="left-voucher">
                </div> -->
                <div class="left-price">
                    <p>
                        <span>Tổng tiền: </span>
                        <span id="totalTicket">{{ totalItem }} đ</span>
                    </p>
                </div>
            </div>
            <div class="right">
                <p class="btn_link_order">
                    <a href="#services-favourite-tab" class="btn btn-sm btn-success waves-effect waves-light"
                        style="margin-right: 5px">
                        <i class="ri-arrow-up-line" style="color: #fff;"></i>
                        Tới danh sách vé
                    </a>
                    <a @click="submit" class="btn  btn-sm "
                        style="background-color: rgb(143, 38, 36); cursor: pointer;color:#fff"><i
                            class="ri-check-double-line" style="color: #fff;"></i> Đặt vé ngay
                    </a>
                </p>
            </div>
        </div>

        <div class="services-order__policy">
            <div class="item">
                <p><strong>Chính sách thanh toán</strong></p>
                <p>{{ config_web.payment_policy }}</p>
            </div>
            <div class="item">
                <p><strong>Chính sách hủy</strong></p>
                <p>{{ config_web.cancellation_policy }}</p>
            </div>
            <div class="item">
                <p><strong>Chính sách hoàn tiền</strong></p>
                <p>{{ config_web.refund_policy }}</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailTicket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">Chi tiết loại vé</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="row">

                            <div id="tbody_modal">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

import { ref } from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    components: { VueDatePicker },

    props: {
        tickets: Object,
        areas: Object,
        json_url_banner: Object,
        config_web: Object,
        user_status: Boolean
    },
    data() {
        return {
            cart: [],
            // user_status: false,
            // selectedDate: localStorage.getItem('use_date') ?? ref(new Date()),
            selectedDate: localStorage.getItem('use_date') ?? null,
            filteredTickets: this.tickets,
            area_id: 'all',
            imageActive: {
                active: "images/icon/ticket-active.png",
                inactive: "images/icon/ticket-inactive.png"
            }
        }
    },
    computed: {
        totalItem() {
            let sum = 0
            this.cart.forEach(function (item) {
                sum += (parseFloat(item.price) * parseFloat(item.qty))
            })
            return this.formatPrice(sum)
        },

    },
    watch: {
        selectedDate(newValue) {
            this.saveUseDate(newValue)

        }
    },
    methods: {
        saveUseDate(newValue) {
            var formattedDate = '';
            if (newValue != null) {
                const date = new Date(newValue);

                const day = date.getDate();
                const month = date.getMonth() + 1;
                const year = date.getFullYear();

                formattedDate = `${month}-${day}-${year}`;
            } else {
                formattedDate = newValue;
            }



            localStorage.setItem('use_date', formattedDate)
        },

        imageForArea(area_id) {
            return this.area_id === area_id ? this.imageActive.active : this.imageActive.inactive;
        },

        submit: function () {
            const cartObject = JSON.parse(localStorage.getItem('cart'))
            const useDate = localStorage.getItem('use_date')
            var has_error = false;

            if (cartObject == null) {
                has_error = true;
                registerOnlineV2.alert_main('Vui lòng chọn vé!', 'error', 'center')
            } else {
                if (cartObject.length === 0) {
                    has_error = true;
                    registerOnlineV2.alert_main('Vui lòng chọn vé!', 'error', 'center')
                }
            }

            // debugger
            if (!this.user_status) {
                if (useDate == undefined || useDate == '' || useDate == 'null' || useDate == 'NaN-NaN-NaN') {
                    has_error = true;
                    registerOnlineV2.alert_main('Vui lòng chọn ngày sử dụng vé!', 'error', 'center')
                } else {
                    const now = new Date();
                    const today = new Date(now);
                    today.setHours(0, 0, 0, 0);

                    const dateCheck = new Date(useDate);

                    if (dateCheck < today) {
                        has_error = true;
                        registerOnlineV2.alert_main('Không thể chọn ngày sử dụng trong quá khứ!', 'error', 'center')
                    }
                }
            }

            if (!has_error) {
                axios.post(route('register_online.saveCart'), {
                    cart: cartObject,
                    useDate: useDate,
                }).then(res => {
                    window.location = route('register_online.previewOrder')
                }).catch(res => {
                    console.log(res)
                })
            }
        },

        getUrlImage(url) {
            return url
        },

        showDetail(id) {
            axios.post(route('register_online.getTicketTypeDetail'), { id: id })
                .then(res => {
                    if (res.data.status === 200) {
                        const detailTicketType = $('#detailTicket')
                        let html = `<h6 class="modal-title">Tên vé: ` + res.data.ticket_name + `</h6>`
                        for (const key in res.data.data) {
                            const data = res.data.data[key]
                            html = html +
                                `<div class="row"><div class="col-12 card" style="margin:20px;background-color: #f7f7f7;width: 90%;"><div class="card-header"
                            style="background-color: #f7f7f7;padding-left: 0 !important;margin-bottom: 10px;"><b style="color:green;font-size: 17px;">` +
                                data.area_name +
                                `</b></div><div class="card-body" style="padding: 0 !important;">`

                            let html_funSpots =
                                `<div><label class="form-check-label">Điểm dịch vụ</label><ul>`
                            for (const keyData in data.getFunSpots) {
                                const funSpots = data.getFunSpots[keyData]
                                html_funSpots = html_funSpots + `<li>` + funSpots + `</li>`

                            }
                            html_funSpots = html_funSpots + `</ul></div>`

                            let html_services =
                                `<div><label class="form-check-label">Dịch vụ</label><ul>`
                            for (const keyData in data.getServices) {
                                const funSpots = data.getServices[keyData]
                                html_services = html_services + `<li>` + funSpots + `</li>`

                            }
                            html_services = html_services + `</ul></div>`


                            html = html + html_funSpots + html_services
                            html = html + `</div></div></div>`
                        }

                        detailTicketType.find('#tbody_modal').empty()
                        detailTicketType.find('#tbody_modal').html(html)
                        detailTicketType.modal('show')
                    }
                })
        },

        onPlusQty(ticket) {
            const foundIndex = this.cart.findIndex(
                (item) => item.id === ticket.id,
            )
            if (foundIndex !== -1) {
                this.cart[foundIndex].qty++
            } else {
                this.cart.push({
                    id: ticket.id,
                    name: ticket.name,
                    price: ticket.price_online,
                    qty: 1,
                })
            }
            this.saveCart()
        },
        onMinusQty(ticket) {
            const foundIndex = this.cart.findIndex(
                (item) => item.id === ticket.id,
            )
            if (foundIndex !== -1 && this.cart[foundIndex].qty > 0) {
                this.cart[foundIndex].qty--
                if (this.cart[foundIndex].qty === 0) {
                    this.cart.splice(foundIndex, 1)
                }
                this.saveCart()
            }
        },

        getQuantity(ticketId) {
            const ticket = this.cart.find((item) => item.id === ticketId)
            return ticket ? ticket.qty : 0
        },

        saveCart() {
            this.saveUseDate(this.selectedDate);
            localStorage.setItem('cart', JSON.stringify(this.cart))
            this.countCart()
        },

        totalPrice(qty, price) {
            return this.formatPrice(qty * price)
        },

        removeFromCart(index) {
            this.cart.splice(index, 1)
            this.saveCart()
        },

        formatPrice(price) {
            return new Intl.NumberFormat().format(price)
        },

        filterArea(area_id) {
            if (area_id === 'all') {
                this.filteredTickets = this.tickets;
            } else {
                this.filteredTickets = this.tickets.filter(ticket => ticket.area_id.some(item => area_id === item.type_id));
            }
            this.area_id = area_id;
        },

        countCart() {
            var count = 0;
            const cartObject = JSON.parse(localStorage.getItem('cart'));
            cartObject.forEach(element => {
                count += element.qty
            });
            document.getElementById('cart-count').textContent = count;
        }

    },
    mounted() {
        const savedCounts = localStorage.getItem('cart')

        if (savedCounts) {
            this.cart = JSON.parse(savedCounts)
            this.cart = this.cart.filter(item => {
                const matchingTicket = this.tickets.find(ticket => ticket.id === item.id);
                return matchingTicket !== undefined;
            });
            this.saveCart()
        } else {
            localStorage.removeItem('cart')
        }
    },
}
</script>
<script setup>
import { ref } from 'vue'
import { vi } from 'date-fns/locale'

const format = (date) => {
    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()

    return `Ngày ${day}/${month}/${year}`
}
</script>
