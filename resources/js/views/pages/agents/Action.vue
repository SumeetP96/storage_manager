<template>
  <div>
    <AppBar backRoute="agents.index" />

    <v-col cols="12" md="10" lg="8" class="mx-auto">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else>
        <div v-if="record.id" class="text-h5 py-5">Update Agent</div>
        <div v-else class="text-h5 py-5">Create new Agent</div>

        <v-row>
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

        <v-row>
          <v-col cols="12" md="3">
            <label class="subtitle-1">Contact no 1
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.contact_1"
              hide-details="auto"
              outlined
              :error-messages="errors.contact_1"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>

          <v-col cols="12" md="3">
            <label class="subtitle-1">Contact no 2
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.contact_2"
              hide-details="auto"
              outlined
              :error-messages="errors.contact_2"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <label class="subtitle-1">Email address
              <span class="red--text text-h6"></span></label>
            <v-text-field
              v-model="record.email"
              hide-details="auto"
              outlined
              :error-messages="errors.email"
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
              v-model="record.remarks"
              hide-details="auto"
              outlined
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-text-field>
          </v-col>
        </v-row>

        <v-btn v-if="record.id" class="mt-6"
          color="indigo" dark :loading="updateButtonLoading"
          @click="updateFromForm($route.params.id, { redirect: 'agents.index' })">
            <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> update agent
        </v-btn>

        <v-btn v-else
          color="indigo" dark class="mt-6" :loading="createButtonLoading"
          @click="createFromForm({ redirect: 'agents.index' })">
            <v-icon class="text-h6 mr-2">mdi-content-save-outline</v-icon> save agent
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
    this.apiRoute = 'agents'

    if (this.$route.params.id) this.loadRecord(this.$route.params.id)
  },
}
</script>
