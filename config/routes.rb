Rails.application.routes.draw do
  devise_scope :user do
    root to: 'devise/sessions#new'
  end

  devise_for :users, controllers: { registrations: 'registrations' }
  # For details on the DSL available within this file, see http://guides.rubyonrails.org/routing.html

  resources :home, only: [:index] do
    member do
      get 'report_back'
      post 'report_back', to: 'home#report_submit', as: 'report_submit'

      get 'request_map_assignment'
      get 'request_assignment', to: 'home#pamphlet_effort', as: 'pamphlet_effort'
    end
  end
end
