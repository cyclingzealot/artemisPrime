class PamphletEffort
  include Mongoid::Document
  field :reportBack, type: String
  field :pollingAreaId, type: String

  belongs_to :user

  #field :volunteer, type: Reference
  #field :pollingArea, type: Reference
  #field :state, type: Reference
end
