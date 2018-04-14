class PamphletEffort
  include Mongoid::Document
  field :volunteer, type: Reference
  field :pollingArea, type: Reference
  field :reportBack, type: String
  field :state, type: Reference
end
