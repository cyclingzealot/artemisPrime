#!/bin/ruby

require 'csv'

#byPartyScript = "./rawData/data/pollbypollbyparty"

#if not File.directory?(byPartyDir)
#    `mkdir -p #{byPartyDir}`
#end

#Pour chaque fichier dans le dossier "by poll"
    # Lire le fichier correspondant pollresults
        # Construire une table correspodante de "Candidat" => "parti"
    # Créer une nouvel instance de circonscription

    # En lisant la première ligne du ficheir "by poll"
        # Construire une table "No de colone => parti"

    # Pour chaque ligne dans ce fichier
        # Créer une instance de bureau
        # Populer les variables de ce bureau
        # Ajouter ce bureau dans une gros tableau de bureaux

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
