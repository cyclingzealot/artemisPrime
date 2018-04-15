class HomeController < ApplicationController

  MAP_SOFTWARE_PORT = 8000
  MAP_SOFTWARE_HOST = 'localhost'

  before_action :authenticate_user!

  def index
  end

  def report_back
    @location_options = options_for_select(["Kanata", "Barrhaven", "Orleans", "Centertown"])
  end

  def report_submit
    # pamphlet_effort = (PamphletEffort.find_by user_id: current_user.id)&.first
    pamphlet_effort = false
    return redirect_to home_index_path, flash: { error: "You don't have an assigned polling area to report about." } unless pamphlet_effort

    pamphlet_effort.update_attribute(reportBack: params[:notes])

    redirect_to home_index_path, flash: { notice: 'Your report has been sent.' }
  end

  def request_map_assignment
    URI uri = URI::HTTP.build({
                         host: HomeController::MAP_SOFTWARE_HOST,
                         port: HomeController::MAP_SOFTWARE_PORT,
                         path: '/assign.html',
                         query: "user_id=#{current_user.id}&file_name=output.geojson"
                     })

    redirect_to uri.to_s
  end

  def pamphlet_effort

        if not params[:test].nil?
            render :inline => "<%= #{params.to_s} %>"
            return
        end
        
        pollingArea = params[:unique_polling_id]
        volunteerId          = params[:user_id]

        v = User.find(volunteerId)


        v or raise "Volunteer not found!"
        puts "Found #{v}"
        
        pe = PamphletEffort.new(
            pollingAreaId:  pollingArea,
            user: v
        )

        pe.save!

		puts "Success!!! Redirecting...."

        #http://54.145.123.77/printable.html?file_name=output.geojson&pod_id=115719

    	URI uri = URI::HTTP.build({
        	host: HomeController::MAP_SOFTWARE_HOST,
            port: HomeController::MAP_SOFTWARE_PORT,
            path: '/printable.html',
            query: "file_name=output.geojson&pod_id=#{pollingArea}" #"unique_polling_id=#{pollingArea}"
        })

    	redirect_to uri.to_s


  end
end
