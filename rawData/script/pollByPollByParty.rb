#!/bin/ruby

require 'csv'

#byPartyScript = "./rawData/data/pollbypollbyparty"

#if not File.directory?(byPartyDir)
#    `mkdir -p #{byPartyDir}`
#end

pollByPollDir = $ARGV[0]
pollResultsDir = $ARGV[1]

pbpPrefix = 'pollbypoll_bureauparbureau'
prPrefix = 'pollresults_resultatsbureau'

allTheRidings = []

#Pour chaque fichier dans le dossier "by poll"
Dir.foreach(pollByPollDir) do |pbpFile|
    next if pbpFile == '.' or pbpFile == '..'
    next if not pbpFile.end_with?('.csv')
    next if not pbpFile.start_with?('pollbypoll_bureauparbureau')

    edId = pbpFile.scan(/\d/).join('')

    # Lire le fichier correspondant pollresults
    prFile = pollResultsDir + prPrefix + edId + '.csv'

    puts "Warning: #{prFile} does not exist" if not File.exists?(prFile)

    candidatePartiEnglish = {}
    partyEnglishFrench = {}
    File.foreach(prFile) {|prLine|
        pollId, firstName, lastName, partyEnglish, partyFrench = readTSVline(l, [4, 10, 12, 13, 14])

        # Construire une table correspodante de "Candidat" => "parti"
        candidatePartiEnglish[firstName + ' ' + lastName] = partyEnglish
        partyEnglishFrench[partyEnglish] = partyFrench
    }

    # Créer une nouvel instance de circonscription
    r = Riding.new()


    # En lisant la première ligne du ficheir "by poll"
    # Construire une table "No de colone => parti"

    # Pour chaque ligne dans ce fichier
    # Créer une instance de bureau
    # Populer les variables de ce bureau
    # Ajouter ce bureau dans une gros tableau de bureaux
    allTheRidings.push r

end

# Imprimer l'entête
# * Riding Num
# * EmrpNum
# * Poll ID
# * Poll Suffix
# * Parties & votes
# * Innefective Votes
# * Surplus votes
# * Total votes
# * Registered voters

# Pour chaque élément du tableau bureaux
# Imprimer une ligne


def readTSVline(l, columns)

   seperator="\t"
   if ! ENV['sep'].nil?
      seperator= ENV['sep']
   end

   row = CSV.parse_line(l, :col_sep => seperator).collect{|x|
      if ! x.nil?
          x.strip;
      end
   }

   ### Pick specify elements of that table
    columns.map {|i| row[i]}
end

