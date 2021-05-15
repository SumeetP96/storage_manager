<template>
  <div>
    <AppBar backRoute="inter_godowns.index" />

    <v-col cols="12" md="11" class="mx-auto">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else>
        <div v-if="record.id" class="text-h5 py-5">Update Transfer</div>
        <div v-else class="text-h5 py-5">Create new Transfer</div>

        <!-- Top -->
        <v-row>
          <!-- Left Side -->
          <v-col cols="12" md="6">

            <!-- Date -->
            <v-row>
              <v-col cols="12" md="5">
                <label class="subtitle-1">Date
                  <span class="red--text text-h6">*</span></label>
                <div class="d-flex align-start">
                  <v-text-field
                    autofocus
                    v-model="record.dateRaw"
                    hide-details="auto"
                    outlined
                    placeholder="Transfer date ( DD-MM-YY )"
                    @blur="formatDate('dateRaw');
                      record.date = flipToYMD(record.dateRaw)"
                    prepend-inner-icon="mdi-calendar"
                    :error-messages="errors.date"
                    :class="$vuetify.theme.dark ? '' : 'white'"
                    class="center-input"
                    dense>
                  </v-text-field>
                  <div v-if="record.dateRaw"
                    class="text-right px-3 ml-2 mt-1 text-h6 indigo white--text rounded">
                      {{ record.date | moment('dddd') }}
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- From Godown -->
            <v-row>
              <v-col cols="11">
                <label class="subtitle-1">From godown
                  <span class="red--text text-h6">*</span></label>
                <div class="d-flex">
                  <div style="width: 100%">
                    <v-autocomplete
                      v-model="record.from_godown_id"
                      hide-details="auto"
                      clearable
                      outlined
                      :loading="fromLoading"
                      :disabled="fromLoading"
                      placeholder="Select godown"
                      auto-select-first
                      :items="godownsWithStock"
                      item-text="name"
                      item-value="id"
                      @input="fetchGodownWithStockDetails()"
                      prepend-inner-icon="mdi-store"
                      :error-messages="errors.from_godown_id"
                      :class="$vuetify.theme.dark ? '' : 'white'"
                      class="left-input"
                      dense>
                    </v-autocomplete>
                    <div v-if="accountDetails.address || accountDetails.contact_1 || accountDetails.contact_2"
                      :class="$vuetify.theme.dark ? '' : 'white'"
                      class="subtitle-2 px-3 py-1 rounded">
                        <span v-if="accountDetails.address">{{ accountDetails.address }}</span>
                        <span v-if="accountDetails.address && (accountDetails.contact_1 || accountDetails.contact_2)"> - </span>
                        <span v-if="!accountDetails.address && (accountDetails.contact_1 || accountDetails.contact_2)">Contact - </span>
                        <span v-if="accountDetails.contact_1" class="success--text">{{ accountDetails.contact_1 }}</span>
                        <span v-if="accountDetails.contact_1 && accountDetails.contact_2">, </span>
                        <span v-if="accountDetails.contact_2" class="success--text">{{ accountDetails.contact_2 }}</span>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- Godown -->
            <v-row no-gutters class="mt-2">
              <v-col cols="11">
                <label class="subtitle-1">To godown
                  <span class="red--text text-h6">*</span></label>
                <div class="d-flex">
                  <div style="width: 100%">
                    <v-autocomplete
                      v-model="record.to_godown_id"
                      hide-details="auto"
                      outlined
                      auto-select-first
                      clearable
                      :disabled="toLoading"
                      :loading="toLoading"
                      placeholder="Select godown"
                      :items="godowns"
                      @input="fetchGodownDetails()"
                      item-text="name"
                      item-value="id"
                      prepend-inner-icon="mdi-store"
                      :error-messages="errors.to_godown_id"
                      :class="$vuetify.theme.dark ? '' : 'white'"
                      class="left-input"
                      dense>
                    </v-autocomplete>
                    <div v-if="godownDetails.address || godownDetails.contact_1 || godownDetails.contact_2"
                      :class="$vuetify.theme.dark ? '' : 'white'"
                      class="subtitle-2 px-3 py-1 rounded">
                        <span v-if="godownDetails.address">{{ godownDetails.address }}</span>
                        <span v-if="godownDetails.address && (godownDetails.contact_1 || godownDetails.contact_2)"> - </span>
                        <span v-if="!godownDetails.address && (godownDetails.contact_1 || godownDetails.contact_2)">Contact - </span>
                        <span v-if="godownDetails.contact_1" class="success--text">{{ godownDetails.contact_1 }}</span>
                        <span v-if="godownDetails.contact_1 && godownDetails.contact_2">, </span>
                        <span v-if="godownDetails.contact_2" class="success--text">{{ godownDetails.contact_2 }}</span>
                    </div>
                  </div>

                  <v-btn v-if="!record.to_godown_id" dark icon class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('godownDialog')">
                      <v-icon>mdi-plus</v-icon>
                  </v-btn>
                  <v-btn v-else dark icon class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('godownDialog', 'godowns', record.to_godown_id)">
                      <v-icon>mdi-circle-edit-outline</v-icon>
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-col> <!-- / Left Side End -->


          <!-- Right Side -->
          <v-col cols="12" md="6" class="pl-8">
            <!-- Product -->
            <v-row>
              <v-col cols="12" class="rounded elevation-1 px-4"
                :class="$vuetify.theme.dark ? 'grey darken-4' : 'white'">

                <v-row no-gutters>
                  <v-col cols="12" md="8">
                    <label class="subtitle-1">Product
                      <span class="red--text text-h6">*</span></label>
                  </v-col>
                  <v-col cols="12" md="4">
                    <div class="subtitle-1 text-right" :class="inputProducts.length > 1 ? 'pr-10' : ''"
                      style="width: 100%">Quantity
                      <span class="red--text text-h6">*</span></div>
                  </v-col>
                </v-row>

                <v-row v-for="(product, index) in inputProducts" :key="index" no-gutters class="mb-2">
                  <v-col cols="12" md="9" class="pr-10">
                    <div style="width: 100%">
                      <v-autocomplete
                        v-model="inputProducts[index].id"
                        hide-details="auto"
                        clearable
                        outlined
                        :disabled="productLoading"
                        :loading="productLoading"
                        :id="`productBox${index}`"
                        placeholder="Select product"
                        auto-select-first
                        :items="products"
                        @input="fetchProductDetails(index)"
                        item-text="name"
                        item-value="id"
                        prepend-inner-icon="mdi-shape-outline"
                        :error-messages="errors[`product_${index}_id`]"
                        :class="$vuetify.theme.dark ? '' : 'white'"
                        class="left-input"
                        dense>
                      </v-autocomplete>
                      <div v-if="productDetails[index].remarks"
                        :class="$vuetify.theme.dark ? '' : 'white'" class="subtitle-2 px-3 py-1 rounded">
                          {{ productDetails[index].remarks }}
                      </div>
                    </div>
                  </v-col>

                  <v-col cols="12" md="3">
                    <div class="d-flex align-start justify-end">
                      <div>
                        <v-text-field
                          v-model="inputProducts[index].quantityRaw"
                          hide-details="auto"
                          outlined
                          @blur="inputProducts[index].quantity = setFormatQuantity(inputProducts[index].quantityRaw)"
                          placeholder="0.00"
                          :error-messages="errors[`product_${index}_quantity`]"
                          :class="$vuetify.theme.dark ? '' : 'white'"
                          class="right-input"
                          dense>
                        </v-text-field>
                        <div v-if="inputProducts[index].id"
                          class="subtitle-2 px-3 py-1 text-right font-weight-bold">
                            <span class="primary--text">{{ formatQuantity(productDetails[index].stock) }}</span>
                            <span class="pink--text pl-1">{{ productDetails[index].unit }}</span>
                        </div>
                      </div>

                      <v-btn v-if="inputProducts.length > 1"
                        dark icon class="error white--text ml-1 elevation-1" tabindex="-1"
                        @click="removeProductInputRow(index)">
                          <v-icon class="text-h6">mdi-close</v-icon>
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>

                <v-btn small color="success" :disabled="invalidAddition()" @click="addProductInputRow()" class="mt-2 mb-1 float-right">
                  <v-icon class="text-h6 mr-2">mdi-plus</v-icon> Add product
                </v-btn>
              </v-col>
            </v-row>
          </v-col> <!-- / Right Side End -->
        </v-row> <!-- / Top End -->

        <!-- Bottom -->
        <div class="overline grey--text mt-8" style="font-size: 0.9rem !important">Additional details</div>
        <v-row>
          <!-- Delivery Slip No -->
          <v-col cols="12" md="2">
            <div class="subtitle-1">Delivery slip number
              <span class="red--text text-h6"></span></div>
            <v-text-field
              v-model="record.delivery_slip_no"
              hide-details="auto"
              placeholder="DELIVERY#"
              outlined
              prepend-inner-icon="mdi-receipt"
              :error-messages="errors.delivery_slip_no"
              :class="$vuetify.theme.dark ? '' : 'white'"
              class="left-input"
              dense>
            </v-text-field>
          </v-col>

          <!-- Transport Details -->
          <v-col cols="12" md="3">
            <div class="subtitle-1">Transport details
              <span class="red--text text-h6"></span></div>
            <v-text-field
              v-model="record.transport_details"
              hide-details="auto"
              outlined
              placeholder="Driver / Vehicle details"
              prepend-inner-icon="mdi-truck-fast-outline"
              :error-messages="errors.transport_details"
              :class="$vuetify.theme.dark ? '' : 'white'"
              class="left-input"
              dense>
            </v-text-field>
          </v-col>
        </v-row> <!-- / Bottom End -->

        <!-- Remarks -->
        <v-row no-gutters class="mt-2">
          <v-col cols="12">
            <label class="subtitle-1">Remarks
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.remarks"
              hide-details="auto"
              outlined
              placeholder="Additional details or notes"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>
        </v-row>

        <div class="my-8">
          <v-btn v-if="record.id"
            color="indigo" dark :loading="updateButtonLoading"
            @click="updateTransfer($route.params.id, 'inter_godowns.index')">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update transfer
          </v-btn>

          <v-btn v-else
            color="indigo" dark :loading="createButtonLoading"
            @click="createTransfer('inter_godowns.index')">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save transfer
          </v-btn>
        </div>

      </div>
    </v-col>

    <!-- Godown Dialog -->
    <v-dialog v-model="godownDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>
            <span v-if="record.to_godown_id">Update Godown</span>
            <span>Create Godown</span>
          </div>
          <v-btn icon @click="closeDialog('godownDialog')"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">

          <v-row>
            <v-col cols="12" md="9">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Name
                <span class="red--text text-h6">*</span></label>
              <v-text-field
                ref="nameBox"
                v-model="dialogRecord.name"
                hide-details="auto"
                outlined
                filled
                autofocus
                :error-messages="dialogErrors.name"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>

            <v-col cols="12" md="3">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Alias
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.alias"
                hide-details="auto"
                outlined
                filled
                :error-messages="dialogErrors.alias"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Address
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.address"
                hide-details="auto"
                outlined
                filled
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="3">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Contact no 1
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.contact_1"
                hide-details="auto"
                outlined
                filled
                :error-messages="dialogErrors.contact_1"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>

            <v-col cols="12" md="3">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Contact no 2
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.contact_2"
                hide-details="auto"
                outlined
                filled
                :error-messages="dialogErrors.contact_2"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Email address
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.email"
                hide-details="auto"
                outlined
                filled
                :error-messages="dialogErrors.email"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <label class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">Remarks
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.remarks"
                hide-details="auto"
                outlined
                filled
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

        </v-card-text>

        <v-card-actions>
          <v-btn v-if="record.to_godown_id"
            color="indigo" text dark :loading="dialogUpdateButton"
            @click="updateDialogRecord(record.to_godown_id, { apiRoute: 'godowns', dialog: 'godownDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update godown
          </v-btn>

          <v-btn v-else
            color="indigo" text dark :loading="dialogCreateButton"
            @click="createDialogRecord({ apiRoute: 'godowns', dialog: 'godownDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save godown
          </v-btn>

          <v-btn color="error" text @click="closeDialog('godownDialog')">
            <v-icon class="text-h6 mr-2">mdi-close</v-icon> cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'
import { InterGodownMixin } from '../../../mixins/transfers/inter_godowns/InterGodownMixin'

export default {
  mixins: [CommonMixin, InterGodownMixin],

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  mounted() {
    this.apiRoute = 'inter_godowns'

    if (this.$route.params.id) {
      this.customFetchRecord(this.$route.params.id, true)
    } else {
      this.customFetchAll()
    }

    this.record.transfer_type = this.transferTypes.interGodown
  },
}
</script>

<style scoped>
  .right-input >>> input {
    text-align: right
  }

  .center-input >>> input {
    text-align: center;
    padding-left: 2px;
  }

  .left-input >>> input {
    padding-left: 10px;
  }
</style>
