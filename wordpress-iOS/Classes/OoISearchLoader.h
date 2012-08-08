//
//  POILoader.h
//  WordPress
//
//  Created by Shakir Ali on 11/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "DataLoader.h"

@protocol OoISearchLoaderDelegate;

@interface OoISearchLoader : DataLoader{
    id<OoISearchLoaderDelegate> delegate;
}

-(void)submitOoISearchRequestWithNEMapPoint:(CLLocationCoordinate2D)ne SWMapPoint:(CLLocationCoordinate2D)sw;
-(void)submitOoISearchRequestWithNEMapPoint:(CLLocationCoordinate2D)ne SWMapPoint:(CLLocationCoordinate2D)sw loadMetaData:(Boolean)loadMetaData;
@property (assign) id<OoISearchLoaderDelegate> delegate;
@end

@protocol OoISearchLoaderDelegate<NSObject>
@optional
-(void)OoISearchLoader:(OoISearchLoader*)loader didFailWithError:(NSError*)error;
-(void)OoISearchLoader:(OoISearchLoader *)loader didLoadOoIData:(NSArray *)objectOfInterests;
@end