class PamphletEffort
  include Mongoid::Document
  field :reportBack, type: String

  belongs_to :user
  belongs_to :pollingArea
  #field :volunteer, type: Reference
  #field :pollingArea, type: Reference
  #field :state, type: Reference
end
