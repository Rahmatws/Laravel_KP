# Struktur Organisasi CV Panca Indra Keemasan

Berikut adalah visualisasi struktur organisasi perusahaan CV Panca Indra Keemasan yang telah disempurnakan.

## 1. Diagram Mermaid (Model Bagan 3 Kolom - Clean)

```mermaid
graph TD
    %% Styling
    classDef owner fill:#000,stroke:#000,stroke-width:2px,color:#fff;
    classDef staff fill:#fff,stroke:#374151,stroke-width:1px,color:#1f2937;
    classDef invis fill:#fff,stroke:#fff,stroke-width:0px,color:#fff,height:0px,width:0px;

    %% Nodes
    Owner[Ludovikus Hasiholan<br/><b>Owner</b>]:::owner
    
    %% Branching Hub
    HubLine(( )):::invis
    Owner --- HubLine

    %% Column 1
    HubLine --- C1_1[Rizal<br/>Customer Service]:::staff
    C1_1 --- C1_2[Dwi<br/>Admin Sales]:::staff
    C1_2 --- C1_3[Putri, Yeti<br/>Admin Stock]:::staff
    C1_3 --- C1_4[Rizki<br/>Admin Marketplace]:::staff

    %% Column 2
    HubLine --- C2_1[Mawar<br/>Marketing]:::staff
    C2_1 --- C2_2[Indah<br/>Accounting & Tax]:::staff
    C2_2 --- C2_3[Maulisa<br/>Accounting & Purchasing]:::staff
    C2_3 --- C2_4[Ari, Angga<br/>Operasional]:::staff

    %% Column 3
    HubLine --- C3_1[Fulky, Asep, Andri<br/>Sales]:::staff
    C3_1 --- C3_2[Gafa<br/>Content Creator]:::staff
    C3_2 --- C3_3[Amel<br/>Talent]:::staff
```

## 2. Diagram PlantUML (Model Bagan 3 Kolom - Clean)

```plantuml
@startuml
skinparam rectangle {
    BackgroundColor White
    BorderColor Black
    Shadowing false
    TextAlignment center
}
skinparam defaultFontName Arial
skinparam linetype ortho
skinparam nodesep 40
skinparam ranksep 30

' Node Owner dengan styling terintegrasi (tanpa double label)
rectangle "Ludovikus Hasiholan\n<b>Owner</b>" as Owner #black;text:white

' Titik pusat penghubung
circle " " as Hub #Black

Owner -down- Hub

' Susunan Staff dalam 3 Kolom Sejajar (Tanpa Kotak Pembungkus)
' Kolom 1
rectangle "Rizal\n(Customer Service)" as S1
rectangle "Dwi\n(Admin Sales)" as S2
rectangle "Putri, Yeti\n(Admin Stock)" as S3
rectangle "Rizki\n(Admin Marketplace)" as S4

' Kolom 2
rectangle "Mawar\n(Marketing)" as S5
rectangle "Indah\n(Accounting & Tax)" as S6
rectangle "Maulisa\n(Accounting & Purchasing)" as S7
rectangle "Ari, Angga\n(Operasional)" as S11

' Kolom 3
rectangle "Fulky, Asep, Andri\n(Sales)" as S8
rectangle "Gafa\n(Content Creator)" as S9
rectangle "Amel\n(Talent)" as S10

' Hubungkan jalur utama ke masing-masing kolom
Hub -down- S1
S1 -down- S2
S2 -down- S3
S3 -down- S4

Hub -down- S5
S5 -down- S6
S6 -down- S7
S7 -down- S11

Hub -down- S8
S8 -down- S9
S9 -down- S10

@enduml
```
