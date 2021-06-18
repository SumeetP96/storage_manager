<template>
  <div>
    <AppBar :backRoute="backRoute" :payload="payload" :disableBack="true"
      :title="$route.params.id ? 'Update sale' : 'Create New Sale'" />

    <v-col cols="12" class="px-8">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else>

        <!-- Top -->
        <div class="overline grey--text" style="font-size: 0.9rem !important">Purchase details</div>
        <div class="d-flex justify-space-between">

          <!-- Left side -->
          <div class="rounded px-4 pb-3 pt-1 d-flex align-start justify-space-between" style="width: 28%"
            :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
              <!-- Purchase No -->
              <div style="width: 30%">
                <div class="subtitle-1 font-weight-bold">Purchase no
                  <span class="red--text text-h6"></span></div>
                <v-text-field
                  v-model="record.purchase_no"
                  hide-details="auto"
                  outlined
                  disabled
                  placeholder="#"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="smaller-input"
                  dense>
                </v-text-field>
              </div>

              <!-- Date -->
              <div style="width: 31%" class="ml-4">
                <label class="subtitle-1 font-weight-bold">Date
                  <span class="red--text text-h6">*</span>
                </label>
                <v-text-field
                  autofocus
                  v-model="record.dateRaw"
                  hide-details="auto"
                  outlined
                  placeholder="DD-MM-YY"
                  @blur="formatDate('dateRaw');
                    record.date = flipToYMD(record.dateRaw)"
                  :error-messages="errors.date"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="smaller-input center-input"
                  dense>
                </v-text-field>
                <div v-if="record.dateRaw" class="subtitle-2 px-2 rounded"
                  :class="$vuetify.theme.dark ? 'primary--text text--lighten-2' : 'primary--text white'">
                    {{ record.date | moment('dddd') }}
                </div>
              </div>

              <!-- Invoice No -->
              <div class="ml-4">
                <div class="subtitle-1 font-weight-bold">Invoice number
                  <span class="red--text text-h6"></span></div>
                <v-text-field
                  v-model="record.invoice_no"
                  hide-details="auto"
                  outlined
                  placeholder="INVOICE#"
                  :error-messages="errors.invoice_no"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="smaller-input"
                  dense>
                </v-text-field>
              </div>
          </div> <!-- / Left side end -->

          <!-- Right side -->
          <div class="rounded px-4 pb-3 pt-1 d-flex align-start justify-space-between" style="width: 71%"
            :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
              <!-- Account -->
              <div class="pr-2" style="width: 50%">
                <label class="subtitle-1 font-weight-bold">From account
                  <span class="red--text text-h6">*</span></label>
                <div class="d-flex">
                  <div style="width: 100%">
                    <v-autocomplete
                      v-model="record.from_godown_id"
                      hide-details="auto"
                      clearable
                      outlined
                      :disabled="fromLoading"
                      :loading="fromLoading"
                      placeholder="Select supplier"
                      auto-select-first
                      :items="accounts"
                      item-text="name"
                      item-value="id"
                      @input="fetchAccountDetails()"
                      prepend-inner-icon="mdi-briefcase-variant-outline"
                      :error-messages="errors.from_godown_id"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                      class="smaller-input left-input"
                      dense>
                    </v-autocomplete>
                    <div v-if="accountDetails.address || accountDetails.contact_1 || accountDetails.contact_2"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                      class="subtitle-2 px-2 py-1 rounded">
                        <span v-if="accountDetails.address">{{ accountDetails.address }}</span>
                        <span v-if="accountDetails.address && (accountDetails.contact_1 || accountDetails.contact_2)"> - </span>
                        <span v-if="!accountDetails.address && (accountDetails.contact_1 || accountDetails.contact_2)">Contact - </span>
                        <span v-if="accountDetails.contact_1" class="success--text">{{ accountDetails.contact_1 }}</span>
                        <span v-if="accountDetails.contact_1 && accountDetails.contact_2">, </span>
                        <span v-if="accountDetails.contact_2" class="success--text">{{ accountDetails.contact_2 }}</span>
                    </div>
                  </div>

                  <v-btn v-if="!record.from_godown_id" dark icon small class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('accountDialog')">
                      <v-icon>mdi-plus</v-icon>
                  </v-btn>
                  <v-btn v-else dark icon small class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('accountDialog', 'godowns', record.from_godown_id)">
                      <v-icon>mdi-circle-edit-outline</v-icon>
                  </v-btn>
                </div>
              </div>

              <!-- Godown -->
              <div class="pl-2" style="width: 50%">
                <label class="subtitle-1 font-weight-bold">To godown
                  <span class="red--text text-h6">*</span></label>
                <div class="d-flex">
                  <div style="width: 100%">
                    <v-autocomplete
                      v-model="record.to_godown_id"
                      hide-details="auto"
                      outlined
                      :disabled="toLoading"
                      :loading="toLoading"
                      auto-select-first
                      clearable
                      placeholder="Select godown"
                      :items="godowns"
                      @input="fetchGodownDetails()"
                      item-text="name"
                      item-value="id"
                      prepend-inner-icon="mdi-store"
                      :error-messages="errors.to_godown_id"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                      class="smaller-input left-input"
                      dense>
                    </v-autocomplete>
                    <div v-if="godownDetails.address || godownDetails.contact_1 || godownDetails.contact_2"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                      class="subtitle-2 px-2 py-1 rounded">
                        <span v-if="godownDetails.address">{{ godownDetails.address }}</span>
                        <span v-if="godownDetails.address && (godownDetails.contact_1 || godownDetails.contact_2)"> - </span>
                        <span v-if="!godownDetails.address && (godownDetails.contact_1 || godownDetails.contact_2)">Contact - </span>
                        <span v-if="godownDetails.contact_1" class="success--text">{{ godownDetails.contact_1 }}</span>
                        <span v-if="godownDetails.contact_1 && godownDetails.contact_2">, </span>
                        <span v-if="godownDetails.contact_2" class="success--text">{{ godownDetails.contact_2 }}</span>
                    </div>
                  </div>

                  <v-btn v-if="!record.to_godown_id" dark icon small class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('godownDialog')">
                      <v-icon>mdi-plus</v-icon>
                  </v-btn>
                  <v-btn v-else dark icon small class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('godownDialog', 'godowns', record.to_godown_id)">
                      <v-icon>mdi-circle-edit-outline</v-icon>
                  </v-btn>
                </div>
              </div>
          </div> <!-- / Right side end -->

        </div>  <!-- / Top End -->


        <!-- Center -->
        <div class="overline grey--text mt-5" style="font-size: 0.9rem !important">Products</div>
        <div class="rounded px-4 py-3"
          :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">

          <table class="invoice-table">
            <!-- Headings -->
            <tr>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-left left-round" style="width: 1%">#</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-left">Product</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right pr-5" style="width: 10%">Lot no</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right pr-5" style="width: 8%">Rent</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right pr-5" style="width: 8%">Labour</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right pr-3" style="width: 10%">Compound</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-left" style="width: 1%"></th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right pr-3" style="width: 10%">Quantity</th>
              <th :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-left" style="width: 1%"></th>
              <td :class="$vuetify.theme.dark ? 'grey darken-4' : 'blue-grey darken-1 white--text'"
                class="text-right right-round" style="width: 1%"></td>
            </tr>

            <!-- Body -->
            <tr v-for="(product, index) in inputProducts" :key="index">
              <td class="subtitle-1 font-weight-bold">{{ index + 1 }}</td>

              <!-- Product -->
              <td>
                <div class="d-flex">
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
                      @change="fetchProductDetails(index)"
                      @click:clear="fetchProductDetails(index, true)"
                      item-text="name"
                      item-value="id"
                      :error-messages="errors[`product_${index}_id`]"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                      class="smaller-input"
                      dense>
                    </v-autocomplete>
                    <div v-if="productDetails[index].remarks"
                      :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'" class="subtitle-2 px-2 rounded">
                        {{ productDetails[index].remarks }}
                    </div>
                  </div>

                  <v-btn v-if="!inputProducts[index].id" dark small icon class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('productDialog', '', '', false, index)">
                      <v-icon>mdi-plus</v-icon>
                  </v-btn>
                  <v-btn v-else dark small icon class="indigo white--text ml-1" elevation="1"
                    @click="openDialog('productDialog', 'products', inputProducts[index].id, true, index)">
                      <v-icon>mdi-circle-edit-outline</v-icon>
                  </v-btn>
                </div>
              </td>

              <!-- Lot number -->
              <td>
                <v-text-field
                  v-model="inputProducts[index].lot_number"
                  hide-details="auto"
                  outlined
                  :disabled="!inputProducts[index].id"
                  :filled="!inputProducts[index].id"
                  placeholder="LOT#"
                  :error-messages="errors[`product_${index}_lot_number`]"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="right-input smaller-input"
                  dense>
                </v-text-field>
              </td>

              <!-- Rent -->
              <td>
                <v-text-field
                  v-model="inputProducts[index].rentRaw"
                  hide-details="auto"
                  outlined
                  @blur="inputProducts[index].rent = setFormatQuantity(inputProducts[index].rentRaw)"
                  :disabled="!inputProducts[index].id"
                  :filled="!inputProducts[index].id"
                  placeholder="0.00"
                  :error-messages="errors[`product_${index}_rent`]"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="right-input smaller-input"
                  dense>
                </v-text-field>
              </td>

              <!-- Labour -->
              <td>
                <v-text-field
                  v-model="inputProducts[index].labourRaw"
                  hide-details="auto"
                  outlined
                  @blur="inputProducts[index].labour = setFormatQuantity(inputProducts[index].labourRaw)"
                  :disabled="!inputProducts[index].id"
                  :filled="!inputProducts[index].id"
                  placeholder="0.00"
                  :error-messages="errors[`product_${index}_labour`]"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="right-input smaller-input"
                  dense>
                </v-text-field>
              </td>

              <!-- Compound -->
              <td class="pr-1">
                <v-text-field
                  v-model="inputProducts[index].compoundQuantityRaw"
                  hide-details="auto"
                  outlined
                  @blur="
                    inputProducts[index].compound_quantity = setFormatQuantity(inputProducts[index].compoundQuantityRaw)
                    calculateQuantity(index)
                    "
                  :disabled="!inputProducts[index].id || !productDetails[index].compoundUnit"
                  :filled="!inputProducts[index].id || !productDetails[index].compoundUnit"
                  placeholder="0.00"
                  :error-messages="errors[`product_${index}_compound_quantity`]"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="right-input smaller-input"
                  dense>
                </v-text-field>
              </td>

              <!-- Unit -->
              <td class="pl-0">
                <div v-if="inputProducts[index].id && productDetails[index].compoundUnit"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="subtitle-2 px-1 py-1 rounded text-right font-weight-bold">
                    <span class="pink--text">
                      {{ productDetails[index].compoundUnit }}<span class="primary--text pl-1">({{ formatQuantity(productDetails[index].packing, 0) }})</span>
                    </span>
                </div>
              </td>

              <!-- Quantity -->
              <td class="pr-1">
                <v-text-field
                  v-model="inputProducts[index].quantityRaw"
                  hide-details="auto"
                  outlined
                  @blur="inputProducts[index].quantity = setFormatQuantity(inputProducts[index].quantityRaw)"
                  @input="
                    inputProducts[index].compoundQuantity = undefined;
                    inputProducts[index].compoundQuantityRaw = undefined;
                    "
                  :disabled="!inputProducts[index].id"
                  :filled="!inputProducts[index].id"
                  placeholder="0.00"
                  :error-messages="errors[`product_${index}_quantity`]"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="right-input smaller-input"
                  dense>
                </v-text-field>
              </td>

              <!-- Unit -->
              <td class="pl-0">
                <div v-if="inputProducts[index].id"
                  :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                  class="subtitle-2 px-1 py-1 rounded text-right font-weight-bold">
                  <span class="pink--text">{{ productDetails[index].unit }}</span>
                </div>
              </td>

              <!-- Delete -->
              <td>
                <v-btn icon small class="ml-1 elevation-1" tabindex="-1"
                  :class="$vuetify.theme.dark ? 'grey darken-4 error--text' : 'white error--text'"
                  @click="removeProductInputRow(index)">
                    <v-icon class="text-h6">mdi-close</v-icon>
                </v-btn>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <th class="text-left left-round footer-th" colspan="2">
                <v-btn small color="success" :disabled="invalidAddition()" @click="addProductInputRow()">
                  <v-icon class="text-h6 mr-2">mdi-plus</v-icon> Add product
                </v-btn>
              </th>
              <th class="footer-th"></th>
              <th class="footer-th"></th>
              <th class="footer-th"></th>
              <th class="footer-th text-right">{{ calculateTotalCompound() }}</th>
              <th class="footer-th"></th>
              <th class="footer-th text-right">{{ calculateTotalQuantity() }}</th>
              <th class="footer-th"></th>
              <th class="right-round footer-th"></th>
            </tr>
          </table>
        </div> <!-- / Center End -->

        <!-- Bottom -->
        <div class="overline grey--text mt-5" style="font-size: 0.9rem !important">Additional details</div>
        <v-row no-gutters class="rounded px-4 pb-3 pt-1"
          :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
          <!-- Agent -->
          <v-col cols="3">
            <label class="subtitle-1 font-weight-bold">Agent
              <span class="red--text text-h6"></span></label>
            <div class="d-flex">
              <v-autocomplete
                v-model="record.agent_id"
                hide-details="auto"
                outlined
                :loading="agentLoading"
                :disabled="agentLoading"
                placeholder="Select agent"
                auto-select-first
                clearable
                :items="agents"
                item-text="name"
                item-value="id"
                prepend-inner-icon="mdi-account-supervisor-circle-outline"
                :error-messages="errors.agent_id"
                :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
                class="left-input smaller-input"
                dense>
              </v-autocomplete>

              <v-btn v-if="!record.agent_id" dark small icon class="indigo white--text ml-1" elevation="1"
                @click="openDialog('agentDialog')">
                  <v-icon>mdi-plus</v-icon>
              </v-btn>
              <v-btn v-else dark small icon class="indigo white--text ml-1" elevation="1"
                @click="openDialog('agentDialog', 'agents', record.agent_id)">
                  <v-icon>mdi-circle-edit-outline</v-icon>
              </v-btn>
            </div>
          </v-col>

          <!-- Order No -->
          <v-col cols="2" class="pl-6">
            <div class="subtitle-1 font-weight-bold">Order number
              <span class="red--text text-h6"></span></div>
            <v-text-field
              v-model="record.order_no"
              hide-details="auto"
              outlined
              placeholder="ORDER#"
              prepend-inner-icon="mdi-clipboard-text-outline"
              :error-messages="errors.order_no"
              :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
              class="left-input smaller-input"
              dense>
            </v-text-field>
          </v-col>

          <!-- Transport Details -->
          <v-col cols="3" class="pl-6">
            <div class="subtitle-1 font-weight-bold">Transport details
              <span class="red--text text-h6"></span></div>
            <v-text-field
              v-model="record.transport_details"
              hide-details="auto"
              outlined
              placeholder="Driver / Vehicle details"
              prepend-inner-icon="mdi-truck-fast-outline"
              :error-messages="errors.transport_details"
              :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
              class="left-input smaller-input"
              dense>
            </v-text-field>
          </v-col>

          <!-- Remarks -->
          <v-col cols="4" class="pl-6">
            <label class="subtitle-1 font-weight-bold">Remarks
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.remarks"
              hide-details="auto"
              outlined
              placeholder="Additional details or notes"
              :class="$vuetify.theme.dark ? 'grey darken-2' : 'white'"
              class="smaller-input"
              dense>
            </v-text-field>
          </v-col>
        </v-row> <!-- / Bottom End -->

        <div class="my-8">
          <v-btn v-if="record.id"
            color="indigo" dark :loading="updateButtonLoading"
            v-shortkey="['alt', 's']" @shortkey="updateTransfer($route.params.id, backRoute, payload)"
            @click="updateTransfer($route.params.id, backRoute, payload)">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update sale
          </v-btn>

          <v-btn v-else
            color="indigo" dark :loading="createButtonLoading"
            v-shortkey="['alt', 's']" @shortkey="createTransfer('purchases.index' )"
            @click="createTransfer('purchases.index')">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save purchase
          </v-btn>
        </div>

      </div>

    </v-col>

    <!-- Account Dialog -->
    <v-dialog v-model="accountDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>
            <span v-if="record.from_godown_id">Update Account</span>
            <span v-else>Create Account</span>
          </div>
          <v-btn icon @click="closeDialog('accountDialog')"><v-icon>mdi-close</v-icon></v-btn>
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
          <v-btn v-if="record.from_godown_id"
            color="indigo" text dark :loading="dialogUpdateButton"
            @click="updateDialogRecord(record.from_godown_id, {
              isAccount: true,
              apiRoute: 'godowns',
              dialog: 'accountDialog'
            })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update account
          </v-btn>

          <v-btn v-else
            color="indigo" text dark :loading="dialogCreateButton"
            @click="createDialogRecord({
              isAccount: true,
              apiRoute: 'godowns',
              dialog: 'accountDialog'
            })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save account
          </v-btn>

          <v-btn color="error" text @click="closeDialog('accountDialog')">
            <v-icon class="text-h6 mr-2">mdi-close</v-icon> cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Product Dialog -->
    <v-dialog v-model="productDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>
            <span v-if="updateMode">Update Product</span>
            <span v-else>Create Product</span>
          </div>
          <v-btn icon @click="closeDialog('productDialog')"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">

          <v-row>
            <v-col cols="12" md="9">
              <label class="subtitle-1">Name
                <span class="red--text text-h6">*</span></label>
              <v-text-field
                ref="nameBox"
                v-model="dialogRecord.name"
                filled
                hide-details="auto"
                outlined
                autofocus
                :error-messages="dialogErrors.name"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>

            <v-col cols="12" md="3">
              <label class="subtitle-1">Alias
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.alias"
                filled
                hide-details="auto"
                outlined
                :error-messages="dialogErrors.alias"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="4">
              <label class="subtitle-1">Lot number
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.lot_number"
                filled
                hide-details="auto"
                outlined
                :error-messages="dialogErrors.lot_number"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

          <v-row align="start">
            <v-col cols="3">
              <label class="subtitle-1">Compound unit
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.compound_unit"
                hide-details="auto"
                outlined
                filled
                :error-messages="errors.compound_unit"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
              <small>( 3 Letters )</small>
            </v-col>

            <v-col cols="2" class="text-center mt-10 subtitle-1 d-flex pl-7">
              <v-icon>mdi-chevron-right</v-icon>
              <span class="font-weight-bold">OF</span>
              <v-icon>mdi-chevron-right</v-icon>
            </v-col>

            <v-col cols="3">
              <label class="subtitle-1">Packing
                <span class="red--text text-h6"></span></label>
              <div class="d-flex align-start">
                <v-text-field
                  v-model="dialogRecord.packingRaw"
                  @blur="dialogRecord.packing = setFormatQuantity(dialogRecord.packingRaw)"
                  hide-details="auto"
                  outlined
                  filled
                  :disabled="disablePackingEdit"
                  :error-messages="errors.packing"
                  :class="$vuetify.theme.dark ? '' : 'white'"
                  dense>
                </v-text-field>

                <v-btn v-if="disablePackingEdit" dark icon class="indigo white--text ml-1" elevation="1" @click="editPacking()">
                  <v-icon>mdi-circle-edit-outline</v-icon>
                </v-btn>
              </div>
            </v-col>

            <v-col cols="3">
              <label class="subtitle-1">Unit
                <span class="red--text text-h6">*</span></label>
              <v-text-field
                v-model="dialogRecord.unit"
                hide-details="auto"
                outlined
                filled
                :error-messages="errors.unit"
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
              <small>( 3 Letters )</small>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <label class="subtitle-1">Remarks
                <span class="red--text text-h6"></span></label>
              <v-text-field
                v-model="dialogRecord.remarks"
                filled
                hide-details="auto"
                outlined
                :class="$vuetify.theme.dark ? '' : 'white'"
                dense>
              </v-text-field>
            </v-col>
          </v-row>

        </v-card-text>

        <v-card-actions>
          <v-btn v-if="updateMode"
            color="indigo" text dark :loading="dialogUpdateButton"
            @click="updateDialogRecord(record.product_id, { apiRoute: 'products', dialog: 'productDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update product
          </v-btn>

          <v-btn v-else
            color="indigo" text dark :loading="dialogCreateButton"
            @click="createDialogRecord({ apiRoute: 'products', dialog: 'productDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save product
          </v-btn>

          <v-btn color="error" text @click="closeDialog('productDialog')">
            <v-icon class="text-h6 mr-2">mdi-close</v-icon> cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Godown Dialog -->
    <v-dialog v-model="godownDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>
            <span v-if="record.to_godown_id">Update Godown</span>
            <span v-else>Create Godown</span>
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
            @click="updateDialogRecord(record.to_godown_id, {
              isAccount: false,
              property: 'to_godown_id',
              apiRoute: 'godowns',
              dialog: 'godownDialog'
            })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update godown
          </v-btn>

          <v-btn v-else
            color="indigo" text dark :loading="dialogCreateButton"
            @click="createDialogRecord({
              isAccount: false,
              property: 'to_godown_id',
              apiRoute: 'godowns',
              dialog: 'godownDialog'
            })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save godown
          </v-btn>

          <v-btn color="error" text @click="closeDialog('godownDialog')">
            <v-icon class="text-h6 mr-2">mdi-close</v-icon> cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Agent Dialog -->
    <v-dialog v-model="agentDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>
            <span v-if="record.agent_id">Update Agent</span>
            <span v-else>Create Agent</span>
          </div>
          <v-btn icon @click="closeDialog('agentDialog')"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">

          <v-row>
            <v-col cols="12" md="9">
              <label class="subtitle-1">Name
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
              <label class="subtitle-1">Alias
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
            <v-col cols="12" md="3">
              <label class="subtitle-1">Contact no 1
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
              <label class="subtitle-1">Contact no 2
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
              <label class="subtitle-1">Email address
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
              <label class="subtitle-1">Remarks
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
          <v-btn v-if="record.agent_id"
            color="indigo" text dark :loading="dialogUpdateButton"
            @click="updateDialogRecord(record.agent_id, { apiRoute: 'agents', dialog: 'agentDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update agent
          </v-btn>

          <v-btn v-else
            color="indigo" text dark :loading="dialogCreateButton"
            @click="createDialogRecord({ apiRoute: 'agents', dialog: 'agentDialog' })">
              <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save agent
          </v-btn>

          <v-btn color="error" text @click="closeDialog('agentDialog')">
            <v-icon class="text-h6 mr-2">mdi-close</v-icon> cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'
import { PurchaseMixin } from '../../../mixins/transfers/purchases/PurchaseMixin'

export default {
  mixins: [CommonMixin, PurchaseMixin],

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  data() {
    return {
      backRoute: '',
      payload: {}
    }
  },

  mounted() {
    this.apiRoute = 'sales'

    if (this.$route.params.backRoute) this.backRoute = this.$route.params.backRoute
    else this.backRoute = 'sales.index'
    if (this.$route.params.payload) this.payload = this.$route.params.payload

    if (this.$route.params.id) {
      this.customFetchRecord(this.$route.params.id, true)
    } else {
      this.axios.get('/api/sales/new')
        .then(response => {
          this.record = response.data.record
          this.customFetchAll()
        })
    }
  },
}
</script>
