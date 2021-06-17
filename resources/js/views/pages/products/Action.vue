<template>
  <div>
    <AppBar backRoute="products.index" />

    <v-col cols="12" md="10" lg="8" class="mx-auto">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else class="rounded px-4 py-4 mt-5" :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
        <div v-if="record.id" class="text-h5">Update Product</div>
        <div v-else class="text-h5">Create new Product</div>

        <v-row class="mt-4">
          <v-col cols="12" md="9">
            <label class="subtitle-1">Name
              <span class="red--text text-h6">*</span></label>
            <v-text-field
              ref="nameBox"
              v-model="record.name"
              hide-details="auto"
              outlined
              autofocus
              :error-messages="errors.name"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>

          <v-col cols="12" md="3">
            <label class="subtitle-1">Alias
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.alias"
              hide-details="auto"
              outlined
              :error-messages="errors.alias"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>
        </v-row>

        <v-row align="start">
          <v-col cols="2">
            <label class="subtitle-1">Compound unit
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.compound_unit"
              hide-details="auto"
              outlined
              :error-messages="errors.compound_unit"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
            <small>( 3 Letters )</small>
          </v-col>

          <v-col cols="1" class="text-center mt-10 subtitle-1 d-flex px-2">
            <v-icon>mdi-chevron-right</v-icon>
            <span class="font-weight-bold">OF</span>
            <v-icon>mdi-chevron-right</v-icon>
          </v-col>

          <v-col cols="2">
            <label class="subtitle-1">Packing
              <span class="red--text text-h6"></span></label>
            <div class="d-flex align-start">
              <v-text-field
                v-model="record.packingRaw"
                hide-details="auto"
                @blur="record.packing = setFormatQuantity(record.packingRaw)"
                outlined
                :filled="disablePackingEdit"
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

          <v-col cols="2">
            <label class="subtitle-1">Unit
              <span class="red--text text-h6">*</span></label>
            <v-text-field
              v-model="record.unit"
              hide-details="auto"
              outlined
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
              v-model="record.remarks"
              hide-details="auto"
              outlined
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>
        </v-row>

        <v-btn v-if="record.id" class="mt-10"
          color="indigo" dark :loading="updateButtonLoading"
          v-shortkey="['alt', 's']" @shortkey="updateFromForm($route.params.id,{ redirect: 'products.index' })"
          @click="updateFromForm($route.params.id,{ redirect: 'products.index' })">
            <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update product
        </v-btn>

        <v-btn v-else
          color="indigo" dark class="mt-10" :loading="createButtonLoading"
          v-shortkey="['alt', 's']" @shortkey="createFromForm({ redirect: 'products.index' })"
          @click="createFromForm({ redirect: 'products.index' })">
            <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save product
        </v-btn>
      </div>

    </v-col>
  </div>
</template>

<script>
import { CommonMixin } from '../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  components: {
    AppBar: require('../../components/AppBar').default
  },

  mounted() {
    this.apiRoute = 'products'

    if (this.$route.params.id) this.loadRecord(this.$route.params.id)
  },
}
</script>

