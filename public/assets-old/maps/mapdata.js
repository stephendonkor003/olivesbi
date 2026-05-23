var simplemaps_countrymap_mapdata={
  main_settings: {
   //General settings
    width: "responsive", //'700' or 'responsive'
    background_color: "#FFFFFF",
    background_transparent: "yes",
    border_color: "#ffffff",
    
    //State defaults
    state_description: "State description",
    state_color: "#88A4BC",
    state_hover_color: "#3B729F",
    state_url: "",
    border_size: 1.5,
    all_states_inactive: "no",
    all_states_zoomable: "yes",
    
    //Location defaults
    location_description: "Location description",
    location_url: "",
    location_color: "#FF0067",
    location_opacity: 0.8,
    location_hover_opacity: 1,
    location_size: 25,
    location_type: "square",
    location_image_source: "frog.png",
    location_border_color: "#FFFFFF",
    location_border: 2,
    location_hover_border: 2.5,
    all_locations_inactive: "no",
    all_locations_hidden: "no",
    
    //Label defaults
    label_color: "#ffffff",
    label_hover_color: "#ffffff",
    label_size: 16,
    label_font: "Arial",
    label_display: "auto",
    label_scale: "yes",
    hide_labels: "no",
    hide_eastern_labels: "no",
   
    //Zoom settings
    zoom: "yes",
    manual_zoom: "yes",
    back_image: "no",
    initial_back: "no",
    initial_zoom: "-1",
    initial_zoom_solo: "no",
    region_opacity: 1,
    region_hover_opacity: 0.6,
    zoom_out_incrementally: "yes",
    zoom_percentage: 0.99,
    zoom_time: 0.5,
    
    //Popup settings
    popup_color: "white",
    popup_opacity: 0.9,
    popup_shadow: 1,
    popup_corners: 5,
    popup_font: "12px/1.5 Verdana, Arial, Helvetica, sans-serif",
    popup_nocss: "no",
    
    //Advanced settings
    div: "map",
    auto_load: "yes",
    url_new_tab: "no",
    images_directory: "default",
    fade_time: 0.1,
    link_text: "View Website",
    popups: "detect",
    state_image_url: "",
    state_image_position: "",
    location_image_url: ""
  },
  state_specific: {
    GHAA: {
      name: "Greater Accra"
    },
    GHAF: {
      name: "Ahafo"
    },
    GHAH: {
      name: "Ashanti"
    },
    GHBE: {
      name: "Bono East"
    },
    GHBO: {
      name: "Bono"
    },
    GHCP: {
      name: "Central"
    },
    GHEP: {
      name: "Eastern"
    },
    GHNE: {
      name: "Northern East"
    },
    GHNP: {
      name: "Northern"
    },
    GHOT: {
      name: "Oti"
    },
    GHSV: {
      name: "Savannah"
    },
    GHTV: {
      name: "Volta"
    },
    GHUE: {
      name: "Upper East"
    },
    GHUW: {
      name: "Upper West"
    },
    GHWN: {
      name: "Western North"
    },
    GHWP: {
      name: "Western"
    }
  },
  locations: {
    "0": {
      name: "Accra",
      lat: "5.554828",
      lng: "-0.200086"
    }
  },
  labels: {
    GHAA: {
      name: "Greater Accra",
      parent_id: "GHAA"
    },
    GHAF: {
      name: "Ahafo",
      parent_id: "GHAF"
    },
    GHAH: {
      name: "Ashanti",
      parent_id: "GHAH"
    },
    GHBE: {
      name: "Bono East",
      parent_id: "GHBE"
    },
    GHBO: {
      name: "Bono",
      parent_id: "GHBO"
    },
    GHCP: {
      name: "Central",
      parent_id: "GHCP"
    },
    GHEP: {
      name: "Eastern",
      parent_id: "GHEP"
    },
    GHNE: {
      name: "Northern East",
      parent_id: "GHNE"
    },
    GHNP: {
      name: "Northern",
      parent_id: "GHNP"
    },
    GHOT: {
      name: "Oti",
      parent_id: "GHOT"
    },
    GHSV: {
      name: "Savannah",
      parent_id: "GHSV"
    },
    GHTV: {
      name: "Volta",
      parent_id: "GHTV"
    },
    GHUE: {
      name: "Upper East",
      parent_id: "GHUE"
    },
    GHUW: {
      name: "Upper West",
      parent_id: "GHUW"
    },
    GHWN: {
      name: "Western North",
      parent_id: "GHWN"
    },
    GHWP: {
      name: "Western",
      parent_id: "GHWP"
    }
  },
  legend: {
    entries: []
  },
  regions: {}
};