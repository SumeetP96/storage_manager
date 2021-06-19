import { EntryMixin } from "./EntryMixin"
import { DialogMixin } from "./DialogMixin"
import { DetailsMixin } from "./DetailsMixin"
import { AutofillMixin } from "./AutofillMixin"

export const TransferMixin = {
  mixins: [
    EntryMixin,
    DialogMixin,
    DetailsMixin,
    AutofillMixin,
  ],

  data() {
    return {
      payload: {},
      backRoute: ''
    }
  }
}
